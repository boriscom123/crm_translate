using Microsoft.AspNetCore.Mvc;
using MySqlConnector;
using System.Text.Json.Serialization;

namespace TranslatorApi.Controllers;

public class TranslatorResponse
{
    public string RequestedDate { get; set; } = string.Empty;
    public bool HasTranslators { get; set; }
    public int Count { get; set; }
    public List<TranslatorDto> Translators { get; set; } = new();
}

public class TranslatorDto
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Email { get; set; } = string.Empty;
    public bool WeekdayAvailable { get; set; }
    public bool WeekendAvailable { get; set; }
}

[ApiController]
[Route("[controller]")]
public class TranslatorController : ControllerBase
{
    private readonly string _connectionString;

    public TranslatorController(IConfiguration configuration)
    {
        _connectionString = configuration.GetConnectionString("DefaultConnection") 
            ?? throw new InvalidOperationException("Строка подключения 'DefaultConnection' не найдена.");
    }

    /// <summary>
    /// Получить список переводчиков, доступных на указанную дату.
    /// Пример: GET /translator?date=2026-01-25
    /// </summary>
    [HttpGet]
    public async Task<ActionResult<TranslatorResponse>> GetTranslatorsByDate([FromQuery] string? date = null)
    {
        try
        {
            // Если дата не передана — используем текущую дату в UTC
            string effectiveDateStr = string.IsNullOrWhiteSpace(date)
                ? TimeZoneInfo.ConvertTimeFromUtc(DateTime.UtcNow, TimeZoneInfo.FindSystemTimeZoneById("Europe/Moscow")).ToString("yyyy-MM-dd")
                : date;

            if (!DateTime.TryParseExact(effectiveDateStr, "yyyy-MM-dd", null, System.Globalization.DateTimeStyles.None, out var targetDate))
            {
                return BadRequest(new { error = "Неверный формат даты. Используйте yyyy-MM-dd." });
            }

            var isWeekend = targetDate.DayOfWeek == DayOfWeek.Saturday || targetDate.DayOfWeek == DayOfWeek.Sunday;

            string query = isWeekend
                ? "SELECT id, name, email, weekday_availability, weekend_availability FROM translators WHERE weekend_availability = 1"
                : "SELECT id, name, email, weekday_availability, weekend_availability FROM translators WHERE weekday_availability = 1";

            var translators = new List<TranslatorDto>();

            using var connection = new MySqlConnection(_connectionString);
            await connection.OpenAsync();

            using var command = new MySqlCommand(query, connection);
            using var reader = await command.ExecuteReaderAsync();

            while (await reader.ReadAsync())
            {
                translators.Add(new TranslatorDto
                {
                    Id = reader.GetInt32("id"),
                    Name = reader.GetString("name"),
                    Email = reader.GetString("email"),
                    WeekdayAvailable = reader.GetBoolean("weekday_availability"),
                    WeekendAvailable = reader.GetBoolean("weekend_availability")
                });
            }

            return Ok(new TranslatorResponse
            {
                RequestedDate = targetDate.ToString("yyyy-MM-dd"),
                HasTranslators = translators.Count > 0,
                Count = translators.Count,
                Translators = translators
            });
        }
        catch (Exception ex)
        {
            Console.WriteLine($"[ERROR] {ex.Message}");
            return StatusCode(500, new { error = "Ошибка при получении данных о переводчиках" });
        }
    }
}