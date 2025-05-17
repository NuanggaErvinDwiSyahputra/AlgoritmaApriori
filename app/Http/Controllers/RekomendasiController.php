<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RekomendasiController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        
         if ($start && $end && $start > $end) {
            return redirect()->route('rekomendasi')->withErrors([
                'error' => 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.',
            ]);
        }
        

        $frekuensi = DB::table('transaksi')
            ->join('variant', 'transaksi.id_variant', '=', 'variant.id_variant')
            ->select('variant.slug', DB::raw('COUNT(*) as frekuensi'))
            ->when($start && $end, function ($query) use ($start, $end) {
                $query->whereBetween('transaksi.tgl_transaksi', [$start, $end]);
            })
            ->groupBy('variant.slug')
            ->orderByDesc('frekuensi')
            ->get();

        return view('rekomendasi.rekomendasi', compact('frekuensi'));
    }

    public function generate(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $minSupport = floatval($request->input('min_support', 0.06));
        $minConfidence = floatval($request->input('min_confidence', 0.06));

        

        // Ambil data transaksi dan kelompokkan berdasarkan no_transaksi
        $transaksi = DB::table('transaksi')
            ->join('variant', 'transaksi.id_variant', '=', 'variant.id_variant')
            ->when($start && $end, fn($q) => $q->whereBetween('transaksi.tgl_transaksi', [$start, $end]))
            ->select('transaksi.no_transaksi', 'variant.slug')
            ->get()
            ->groupBy('no_transaksi')
            ->map(fn($items) => $items->pluck('slug')->unique()->sort()->values()->toArray())
            ->values();

        $totalTransactions = $transaksi->count();

        // Langkah 1: Frekuensi item tunggal
        $itemCounts = [];
        foreach ($transaksi as $trx) {
            foreach ($trx as $item) {
                $itemCounts[$item] = ($itemCounts[$item] ?? 0) + 1;
            }
        }

        $frequentItems = [];
        foreach ($itemCounts as $item => $count) {
            $support = $count / $totalTransactions;
            if ($support >= $minSupport) {
                $frequentItems[json_encode([$item])] = [
                    'items' => [$item],
                    'support' => $support,
                    'count' => $count,
                ];
            }
        }

        // Langkah 2: Generate kombinasi itemset dan iterasi apriori
        $k = 2;
        $frequentItemsets = $frequentItems;
        while (true) {
            $newCandidates = $this->generateCandidates(array_values($frequentItems), $k);
            $candidateCounts = [];

            foreach ($transaksi as $trx) {
                $trx = collect($trx);
                foreach ($newCandidates as $candidate) {
                    if ($trx->intersect($candidate)->count() === count($candidate)) {
                        $key = json_encode($candidate);
                        $candidateCounts[$key] = ($candidateCounts[$key] ?? 0) + 1;
                    }
                }
            }

            $frequentItems = [];
            foreach ($candidateCounts as $key => $count) {
                $support = $count / $totalTransactions;
                if ($support >= $minSupport) {
                    $frequentItems[$key] = [
                        'items' => json_decode($key),
                        'support' => $support,
                        'count' => $count,
                    ];
                }
            }

            if (empty($frequentItems)) break;
            $frequentItemsets += $frequentItems;
            $k++;
        }

        // Langkah 3: Hitung confidence dan lift ratio
        $rules = [];
        foreach ($frequentItemsets as $set) {
            if (count($set['items']) < 2) continue;

            $itemsetSupport = $set['support'];
            $items = $set['items'];

            $subsets = $this->generateSubsets($items);
            foreach ($subsets as $subset) {
                $remain = array_values(array_diff($items, $subset));
                if (empty($remain)) continue;

                $subsetKey = json_encode($subset);
                $remainKey = json_encode($remain);

                // $alreadyExists = collect($rules)->contains(function ($rule) use ($subset, $remain) {
                //     return (
                //         $rule['antecedent'] === $remain &&
                //         $rule['consequent'] === $subset
                //     );
                // });

                if (isset($frequentItemsets[$subsetKey]) && isset($frequentItemsets[$remainKey])) {
                    $confidence = $itemsetSupport / $frequentItemsets[$subsetKey]['support'];
                    $lift = $confidence / $frequentItemsets[$remainKey]['support'];

                    if ( $confidence >= $minConfidence) { //Tambahkan !$alreadyExists && jika mau menghindari duplikasi
                        $rules[] = [
                            'antecedent' => $subset,
                            'consequent' => $remain,
                            'support' => round($itemsetSupport, 4),
                            'confidence' => round($confidence, 4),
                            'lift' => round($lift, 4),
                            'frekuensi' => $set['count'],
                        ];
                    }
                }
            }
        }
        usort($rules, function ($a, $b) {
            // Urutkan support descending
            if ($a['support'] != $b['support']) {
                return $b['support'] <=> $a['support'];
            }

            // Jika support sama, urutkan confidence descending
            if ($a['confidence'] != $b['confidence']) {
                return $b['confidence'] <=> $a['confidence'];
            }

            // Jika confidence juga sama, urutkan lift descending
            return $b['lift'] <=> $a['lift'];
        });
        return view('rekomendasi.hasilrekomendasi', compact('rules'));
    }

private function generateCandidates(array $itemsets, int $k): array
{
    $candidates = [];
    $count = count($itemsets);
    for ($i = 0; $i < $count; $i++) {
        for ($j = $i + 1; $j < $count; $j++) {
            $merged = array_unique(array_merge($itemsets[$i]['items'], $itemsets[$j]['items']));
            sort($merged);
            if (count($merged) === $k) {
                $candidates[] = $merged;
            }
        }
    }
    return array_map("unserialize", array_unique(array_map("serialize", $candidates)));
}

private function generateSubsets(array $items): array
{
    $results = [];
    $count = count($items);
    $total = pow(2, $count);
    for ($i = 1; $i < $total - 1; $i++) {
        $subset = [];
        for ($j = 0; $j < $count; $j++) {
            if (($i >> $j) & 1) {
                $subset[] = $items[$j];
            }
        }
        $results[] = $subset;
    }
    return $results;
}

}
