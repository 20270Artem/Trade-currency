<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade;
use GuzzleHttp\Client;
use App\Jobs\ProcessTradeResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpClient\HttpClient;

class TradeController extends Controller
{

    public function showDashboard()
    {
    
        $trades = Trade::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();


        return view('dashboard', ['trades' => $trades]);
    }
    

    
    public function placeBet(Request $request)
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

            $latestData = reset($data['Time Series (1min)']);
            $usdToRubRate = $latestData['1. open'];

            $user = Auth::user();
            $betAmount = $request->input('bet');
            $direction = $request->input('direction');
            
            if ($user->balanceRUB >= $betAmount) {
                $user->balanceRUB -= $betAmount;
                $user->save();

                $trade = new Trade([
                    'user_id' => $user->id,
                    'amount' => $betAmount,
                    'direction' => $direction,
                    'current_price' => $usdToRubRate,

                ]);
                $trade->save();
                

                return redirect()->back()->with('success', 'Ставка размещена успешно');
            } else {
                return redirect()->back()->with('error', 'Недостаточно средств на балансе');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Произошла ошибка при получении курса валюты');
        }
    }

}
