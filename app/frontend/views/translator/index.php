<?php

/* @var $this yii\web\View */
/* @var $translators common\models\Translator[] */

use yii\helpers\Html;

$this->title = 'Переводчики';

// Convert the translator objects to JSON for use in Vue.js
$translatorsJson = json_encode(array_map(function($translator) {
    return [
        'id' => $translator->id,
        'name' => $translator->name,
        'email' => $translator->email,
        'weekday_availability' => $translator->weekday_availability,
        'weekend_availability' => $translator->weekend_availability,
    ];
}, $translators));
?>

<div class="translator-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Server-Side Rendered Content for SEO -->
    <div id="translator-ssr-content">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Выберите дату:</label>
                <input type="date" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
        </div>

        <h2>Доступные переводчики на <span id="current-date-display"><?= date('j F Y года') ?></span></h2>

        <div class="row">
            <?php
            $currentDate = date('Y-m-d');
            $availableTranslators = array_filter($translators, function($translator) use ($currentDate) {
                return $translator->isAvailableOnDate($currentDate);
            });

            if (!empty($availableTranslators)):
                foreach ($availableTranslators as $translator): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= Html::encode($translator->name) ?></h5>
                                <p class="card-text">Email: <?= Html::encode($translator->email) ?></p>
                                <p class="card-text">Доступность по будням: <?= $translator->weekday_availability ? 'Доступен' : 'Недоступен' ?></p>
                                <p class="card-text">Доступность по выходным: <?= $translator->weekend_availability ? 'Доступен' : 'Недоступен' ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info mt-3">
                        Нет доступных переводчиков на выбранную дату.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Vue.js Application that will replace SSR content -->
    <div id="translator-app" v-cloak style="display: none;">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="date-picker" class="form-label">Выберите дату:</label>
                <input type="date"
                       id="date-picker"
                       class="form-control"
                       v-model="selectedDate"
                       @change="updateAvailability">
            </div>
        </div>

        <h2>Доступные переводчики на {{ formattedSelectedDate }}</h2>

        <div v-if="loading" class="loading">
            Загрузка переводчиков...
        </div>

        <div v-if="error" class="alert alert-danger">
            Ошибка: {{ error }}
        </div>

        <div v-if="!loading && !error" class="row">
            <div v-for="translator in filteredTranslators" :key="translator.id" class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ translator.name }}</h5>
                        <p class="card-text">Email: {{ translator.email }}</p>
                        <p class="card-text">Доступность по будням: {{ translator.weekday_availability ? 'Доступен' : 'Недоступен' }}</p>
                        <p class="card-text">Доступность по выходным: {{ translator.weekend_availability ? 'Доступен' : 'Недоступен' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="filteredTranslators.length === 0 && !loading && !error" class="alert alert-info mt-3">
            Нет доступных переводчиков на выбранную дату.
        </div>
    </div>

    <!-- Load Vue.js 3 from CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <!-- Initialize the Vue.js application -->
    <script>
        const { createApp, ref, computed, onMounted } = Vue;

        createApp({
            setup() {
                // Reactive data
                const translators = ref(<?= $translatorsJson ?: '[]' ?>);
                const selectedDate = ref(new Date().toISOString().split('T')[0]); // Today's date in YYYY-MM-DD format
                const loading = ref(false);
                const error = ref(null);

                // Computed property to format the selected date
                const formattedSelectedDate = computed(() => {
                    const options = { day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC' };
                    const date = new Date(selectedDate.value);
                    return date.toLocaleDateString('ru-RU', options) + ' года';
                });

                // Function to check if a translator is available on a specific date
                const isTranslatorAvailable = (translator, dateStr) => {
                    const date = new Date(dateStr);
                    const dayOfWeek = date.getDay(); // 0 = Sunday, 6 = Saturday
                    const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);

                    if (isWeekend) {
                        return translator.weekend_availability;
                    } else {
                        return translator.weekday_availability;
                    }
                };

                // Computed property to get filtered translators based on selected date
                const filteredTranslators = computed(() => {
                    if (!translators.value || translators.value.length === 0) {
                        return [];
                    }

                    return translators.value.filter(translator =>
                        isTranslatorAvailable(translator, selectedDate.value)
                    );
                });

                // Function to update availability when date changes
                const updateAvailability = () => {
                    // This is handled automatically by Vue's reactivity
                };

                // Initialize the app
                onMounted(() => {
                    // Show the Vue app and hide the SSR content
                    const ssrContent = document.getElementById('translator-ssr-content');
                    const vueApp = document.getElementById('translator-app');

                    if (ssrContent) {
                        ssrContent.style.display = 'none';
                    }

                    if (vueApp) {
                        vueApp.style.display = 'block';
                    }
                });

                return {
                    translators,
                    selectedDate,
                    loading,
                    error,
                    formattedSelectedDate,
                    filteredTranslators,
                    updateAvailability
                };
            }
        }).mount('#translator-app');
    </script>

    <style>
        .card {
            border: 1px solid #ddd;
            border-radius: 0.25rem;
        }

        .card-body {
            padding: 1rem;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -0.5rem;
        }

        .col-md-4 {
            flex: 0 0 auto;
            width: 33.333333%;
            padding: 0 0.5rem;
        }

        .form-label {
            font-weight: bold;
        }

        .loading {
            text-align: center;
            padding: 20px;
            font-size: 18px;
        }

        /* Hide Vue.js content until Vue is loaded */
        [v-cloak] {
            display: none;
        }

        /* Style for initial content */
        .translator-initial-content {
            /* Initially shown, hidden when Vue takes over */
        }
    </style>

    <?php if (empty($translators)): ?>
        <p>No translators available.</p>
    <?php endif; ?>
</div>