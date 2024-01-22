<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Trade USD/RUB') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">




                    <div id="tradingview-widget-container" class="tradingview-widget-container">
                        <div id="tradingview-widget" class="tradingview-widget-container__widget"></div>
                        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                            {
                                "width": "1168",
                                "height": "610",
                                "symbol": "FX_IDC:USDRUB",
                                "interval": "1",
                                "timezone": "Asia/Hong_Kong",
                                "theme": "dark",
                                "style": "8",
                                "locale": "ru",
                                "enable_publishing": false,
                                "withdateranges": true,
                                "hide_legend": true,
                                "hide_side_toolbar": false,
                                "save_image": false,
                                "hide_volume": true,
                                "support_host": "https://www.tradingview.com"
                            }
                        </script>
                    </div>






                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var widget = new TradingView.widget({
                                "width": "1168",
                                "height": "610",
                                "symbol": "FX_IDC:USDRUB",
                                "interval": "1",
                                "timezone": "Asia/Hong_Kong",
                                "theme": "dark",
                                "style": "8",
                                "locale": "ru",
                                "enable_publishing": false,
                                "withdateranges": true,
                                "hide_legend": true,
                                "hide_side_toolbar": false,
                                "save_image": false,
                                "hide_volume": true,
                                "support_host": "https://www.tradingview.com"

                            });

                            function setDirection(direction, form) {
                                var currentPrice = widget.chart().getActiveChart().symbolInterval().lastValue();
                                document.getElementById('current-price').value = currentPrice;
                                form.submit();
                            }

                            document.getElementById('form-up').addEventListener('submit', function(event) {
                                event.preventDefault();
                                setDirection('up', this);
                            });

                            document.getElementById('form-down').addEventListener('submit', function(event) {
                                event.preventDefault();
                                setDirection('down', this);
                            });
                        });
                    </script>




                    <form method="post" action="{{ route('place-bet') }}">
                        @csrf
                        <input type="hidden" name="current_price" id="current-price" value="">

                        <div>
                            <x-input-label for="bet" :value="__('Ставка rub')" />
                            <x-text-input id="bet" name="bet" type="number" class="mt-1 block w-full"
                                autocomplete="bet" />
                        </div>
                        <br>

                        <div>
                            <label for="direction" class="block text-sm font-medium text-white-700">Выберите направление
                                курса</label>

                            <div class="flex">
                                <div class="flex items-center gap-4">
                                    <x-primary-button type="submit" name="direction"
                                        value="up">{{ __('Вверх') }}</x-primary-button>
                                </div>

                                <div class="margin10-10"></div>

                                <div class="flex items-center gap-4">
                                    <x-primary-button type="submit" name="direction"
                                        value="down">{{ __('Вниз') }}</x-primary-button>
                                </div>
                            </div>
                        </div>
                    </form>










                </div>




            </div>

        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        @foreach ($trades as $trade)
                            <div class="trade-container">
                                <p><strong>Ваша ставка:</strong> {{ $trade->amount }}</p>
                                <p><strong>Направлление курса:</strong> {{ $trade->direction }}</p>
                                <p><strong>Курс на момент ставки:</strong> {{ $trade->current_price }}</p>
                                <p><strong>Курс по окончанию таймера:</strong> {{ $trade->result_price }}</p>
                                <p><strong>Результат:</strong> {{ $trade->result }}</p>
                                <p><strong>Дата создания:</strong> {{ $trade->created_at }}</p>
                            </div>
                        @endforeach


                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
