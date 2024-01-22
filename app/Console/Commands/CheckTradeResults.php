<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Trade;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpClient\HttpClient;
use Log;

class CheckTradeResults extends Command
{
    protected $signature = 'check:trade-results';
    protected $description = 'Check trade results and update user balances';

    public function handle()
    {
        try {
            Log::info('Запущена проверка результатов ставок: ' . now());


            $activeTrades = Trade::whereNull('result')->get();

            foreach ($activeTrades as $trade) {
                $createdAt = Carbon::parse($trade->created_at);
                $currentTime = Carbon::now();


                if ($createdAt->diffInMinutes($currentTime) >= 3) {

                    $currentPrice = $trade->current_price;

                    $resultPrice = $this->getCurrentPrice($trade->symbol);

                    Log::info('Текущая цена из базы данных: ' . $currentPrice);
                    Log::info('Цена из внешнего источника: ' . $resultPrice);

                    if (
                        ($trade->direction === 'up' && $currentPrice < $resultPrice) ||
                        ($trade->direction === 'down' && $currentPrice > $resultPrice)
                    ) {

                        $trade->result = 'победа';

                        $trade->user->balanceRUB += $trade->amount * 1.5;

                        $trade->result_price = $resultPrice;

                        $trade->save();

                        $trade->user->save();

                        Log::info('Результат ставки успешно обработан');
                    } else {

                        $trade->result_price = $resultPrice;

                        $trade->result = 'проигрыш';



                        $trade->save();

                        Log::info('Ставка проиграна');
                    }
                }
            }

            Log::info('Проверка результатов ставок завершена: ' . now());
        } catch (\Throwable $e) {
            Log::error('Ошибка при выполнении команды check:trade-results: ' . $e->getMessage());
        }
    }




    private function getCurrentPrice($symbol)
    {
        $apiKey = 'T851QKUO5XIJDL3I';
        $symbol = 'USDRUB';
        $interval = '1min';

        $client = new Client();

        try {
            $response = $client->get("https://www.alphavantage.co/query", [
                'query' => [
                    'function' => 'TIME_SERIES_INTRADAY',
                    'symbol' => $symbol,
                    'interval' => $interval,
                    'apikey' => $apiKey,
                ],
                'verify' => false,
            ]);

            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);

            Log::info('Ответ от внешнего источника: ' . json_encode($data));

            if (isset($data['Time Series (1min)']) && is_array($data['Time Series (1min)'])) {
                $latestData = reset($data['Time Series (1min)']);
                return $latestData['1. open'];
            } else {
                return null;
            }
        } catch (\Throwable $e) {
            Log::error('Ошибка при получении цены от внешнего источника: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return null;
        }
    }
}
