<!DOCTYPE html>
<html>

{{-- @dd($daterange) --}}

<head>
    <style>
        #customers {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #customers td,
        #customers th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #customers tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #customers tr:hover {
            background-color: #ddd;
        }

        #customers th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #04AA6D;
            color: white;
        }

        h1 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 22px;
        }

    </style>
</head>

<body>

    @if ($from == null && $to == null)
        <h1>Detail Komisi {{ $employee->name }} ({{ $tanggal_terlama }} &nbsp; - &nbsp; {{ $tanggal_terbaru }})</h1>
    @else
    <h1>Detail Komisi {{ $employee->name }} ({{ $from }} &nbsp; - &nbsp; {{ $to }})</h1>
    @endif

    {{-- @dd('a'); --}}
    <table id="customers">
        <thead>
            <tr>
                <th>#</th>
                <th>Nopol</th>
                <th>Layanan yang dikerjakan</th>
                <th>Tanggal</th>
                <th>Komisi</th>
                {{-- <th>Kasbon</th> --}}
            </tr>
        </thead>
        <tbody>
            @php
            $no = 1;
            $total_commission = 0;
            $total_kasbon = 0;
            @endphp

            @foreach ($employee_kasbon as $kasbon)
                @php
                    $total_kasbon += $kasbon->nominal;
                    $sisa_nominal = $kasbon->kasbon_maksimal - $total_kasbon;
                @endphp
            @endforeach

            @foreach ($daterange as $data)
            {{-- @foreach ($data->employees->where('id', $id) as $worker) --}}
            <tr id="tbody">
                <td>{{ $no++ }}</td>
                {{-- @dd($data) --}}
                <td>{{ $data->transactions->customer }}</td>
                <td>
                    <ul>
                        @php
                        $row_commission = 0;
                        // $commission_total = 0;
                        @endphp
                        {{-- <li>{{ $data->employee_products->service }}</li> --}}
                        @foreach ($transaction_product->where('transaction_id', $data->transactions->id) as $product)
                        @php
                        $row_commission += $product->commission;
                        // $commission_total +=/ $product->commission;
                        // dd($commission_total)
                        @endphp
                        <li>{{ $product->employee_products->service }} (@currency($product->commission))</li>
                        @php
                        $total_commission += $product->commission;
                        @endphp
                        @endforeach
                        {{-- @if ($worker->pivot->status == 'normal')
                                @foreach ($data->products->where('status', 0) as $item)
                                    <li>
                                        {{ $item->service }}
                        </li>
                        @endforeach
                        @else
                        @foreach ($data->products as $item)
                        <li>
                            {{ $item->service }}
                        </li>
                        @endforeach
                        @endif --}}
                    </ul>
                </td>
                <td style="text-align: center">
                    {{ $data->transactions->created_at->translatedFormat('j F Y - H:i:s') }}
                </td>
                {{-- <td style="text-align: center">@currency($worker->pivot->commission)</td> --}}
                {{-- <td style="text-align: center">
                            @if ($worker->pivot->status == "extra")
                                @currency($worker->pivot->commission) (@currency($extra_price) x 30% / {{ $extra_workers }})
                @else
                @currency($worker->pivot->commission) (@currency($normal_price) x 30% / {{ $total_workers }} )
                @endif
                </td> --}}
                <td style="text-align: center">@currency($row_commission) </td>
            </tr>
            {{-- @php
            $total_commission += $worker->pivot->commission
            @endphp --}}

            {{-- @endforeach --}}
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" text-align="center"></td>
                <td class="table-primary" style="text-align: center">Total : </td>
                <td class="table-primary" style="text-align: center">@currency($total_commission)</td>
            </tr>
            <th class="text-align: center;" colspan="5  ">Kasbon
                <tr>
                    <td colspan="1" text-align="center"></td>
                    <td class="table-primary" style="text-align: center">Total Kasbon : </td>
                    <td class="table-primary" style="text-align: center">@currency($total_kasbon)</td>
                    <td class="table-primary" style="text-align: center">Sisa Kasbon</td>
                    <td class="table-primary" style="text-align: center">@currency($sisa_nominal)</td>
                </tr>
            </th>
        </tfoot>
    </table>

</body>

</html>
