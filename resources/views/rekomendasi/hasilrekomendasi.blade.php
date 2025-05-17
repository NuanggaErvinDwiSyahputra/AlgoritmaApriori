@extends('index')

@section('tittle','Hasil Bundling Menu')

@section('content')
    <div class="card shadow-lg mt-3" style="border-radius : 2rem;">
        <div class="card-body">
            <h4 class="text-center">Hasil Rekomendasi Bundling</h4>
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Antecedent</th>
                            <th>Consequent</th>
                            <th>Support</th>
                            <th>Confidence</th>
                            <th>Lift Ratio</th>
                            <th>Frekuensi</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rules as $i => $rule)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ implode(', ', $rule['antecedent']) }}</td>
                                <td>{{ implode(', ', $rule['consequent']) }}</td>
                                <td>{{ $rule['support'] }}</td>
                                <td>{{ $rule['confidence'] }}</td>
                                <td>{{ $rule['lift'] }}</td>
                                  <td>{{ $rule['frekuensi'] }}</td> <!-- Tambahan -->
                            </tr>
                        @empty
                            <tr><td colspan="6">Tidak ada data yang memenuhi kriteria.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
