@include('pdf.partials._styles')

<style>
    .border {
        border: 1px solid black !important;
        border-collapse: collapse;
        padding: 3px;
    }
    .row-border {
        border: 1px solid black;
    }
    .bg-grey {
        background-color: #c3c3c3;
    }

    table {
        page-break-inside: avoid;
    }
</style>

<div class="container">
    @foreach($form->measurements as $date => $measurements)
        <table width="100%">
            <tr class="border">
                <td colspan="20" class="text-center bg-grey border"><strong>@if ($loop->first) Initial @endif Inspection Date: </strong>{{ $date }}</td>
            </tr>
            <tr>
                <td colspan="4" class="bg-grey text-center border" width="20%">Outside</td>
                <td colspan="4" class="text-center border" width="20%"> Unaffected</td>
                <td colspan="4" class="bg-grey text-center border" width="20%"> Affected</td>
                <td colspan="4" class="text-center border" width="20%"> Dehumidifier 1</td>
                <td colspan="4" class="bg-grey text-center border" width="20%"> Dehumidifier 2</td>
            </tr>
            <tr>
                <td class="bg-grey text-center border" width="5%">TEMP</td>
                <td class="bg-grey text-center border" width="5%">RH%</td>
                <td class="bg-grey text-center border" width="5%">GPP</td>
                <td class="bg-grey text-center border" width="5%">DEW</td>
                <td class="text-center border" width="5%">TEMP</td>
                <td class="text-center border" width="5%">RH%</td>
                <td class="text-center border" width="5%">GPP</td>
                <td class="text-center border" width="5%">DEW</td>
                <td class="bg-grey text-center border" width="5%">TEMP</td>
                <td class="bg-grey text-center border" width="5%">RH%</td>
                <td class="bg-grey text-center border" width="5%">GPP</td>
                <td class="bg-grey text-center border" width="5%">DEW</td>
                <td class="text-center border" width="5%">TEMP</td>
                <td class="text-center border" width="5%">RH%</td>
                <td class="text-center border" width="5%">GPP</td>
                <td class="text-center border" width="5%">DEW</td>
                <td class="bg-grey text-center border" width="5%">TEMP</td>
                <td class="bg-grey text-center border" width="5%">RH%</td>
                <td class="bg-grey text-center border" width="5%">GPP</td>
                <td class="bg-grey text-center border" width="5%">DEW</td>
            </tr>
            <tr><td colspan="20">&nbsp;</td></tr>
        </table>
        @foreach($measurements['areas'] as $area)
            <table width="100%">
                <tr class="border">
                    <td colspan="20" class="text-center border"><strong>{{ $area['title'] }}</strong></td>
                </tr>
                @foreach($area['measurements'] as $dayMeasurement)
                    <tr>
                        @foreach($dayMeasurement->outside as $data)
                            <td width="5%" class="bg-grey text-center border">{{ $data ?? '&nbsp;' }}</td>
                        @endforeach
                        @foreach($dayMeasurement->unaffected as $data)
                            <td width="5%" class="text-center border">{{ $data ?? '&nbsp;' }}</td>
                        @endforeach
                        @foreach($dayMeasurement->affected as $data)
                            <td width="5%" class="bg-grey text-center border">{{ $data ?? '&nbsp;' }}</td>
                        @endforeach
                        @foreach($dayMeasurement->dehumidifier as $data)
                            <td width="5%" class="text-center border">{{ $data ?? '&nbsp;' }}</td>
                        @endforeach
                        @foreach($dayMeasurement->hvac as $data)
                            <td width="5%" class="bg-grey text-center border">{{ $data ?? '&nbsp;' }}</td>
                        @endforeach
                    </tr>
                    <tr><td colspan="20">&nbsp;</td></tr>
                @endforeach
            </table>
        @endforeach
    @endforeach
    {{--@foreach($form->days as $day)--}}
    {{--<table width="100%">--}}
        {{--<tr class="border">--}}
            {{--<td colspan="8" class="text-center bg-grey border"><strong>@if ($loop->first) Initial @endif Inspection Date: </strong>{{ $day->date }}</td>--}}
        {{--</tr>--}}
        {{--<tr><td colspan="8">&nbsp;</td></tr>--}}
    {{--</table>--}}

        {{--@foreach ($day->days_data as $dayData)--}}
            {{--<table width="100%">--}}
                {{--<tr>--}}
                    {{--<th colspan="8" class="border">{{ $dayData->area->standard_area->title }}</th>--}}
                {{--</tr>--}}
                {{--<tr><td colspan="8">&nbsp;</td></tr>--}}
                {{--<tr>--}}
                    {{--@foreach($dayData->data as $data)--}}
                        {{--<td width="12%" class="border text-center @if($loop->index == 0 || $loop->index % 2 === 0) bg-grey @endif">{{ $data['structure'] ?? 'n/a' }}</td>--}}
                    {{--@endforeach--}}
                {{--</tr>--}}
                {{--<tr>--}}
                    {{--@foreach($dayData->data as $data)--}}
                        {{--<td width="12%" class="border text-center @if($loop->index == 0 || $loop->index % 2 === 0) bg-grey @endif">{{ $data['material'] ?? 'n/a' }}</td>--}}
                    {{--@endforeach--}}
                {{--</tr>--}}
                {{--<tr>--}}
                    {{--@foreach($dayData->data as $data)--}}
                        {{--<td width="12%" class="border text-center @if($loop->index == 0 || $loop->index % 2 === 0) bg-grey @endif">{{ $data['value'] ?? 'n/a' }}</td>--}}
                    {{--@endforeach--}}
                {{--</tr>--}}
                {{--<tr><td colspan="8">&nbsp;</td></tr>--}}
            {{--</table>--}}
        {{--@endforeach--}}
    {{--@endforeach--}}
</div>
