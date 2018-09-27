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
    @foreach($form->days as $day)
    <table width="100%">
        <tr class="border">
            <td colspan="8" class="text-center bg-grey border"><strong>@if ($loop->first) Initial @endif Inspection Date: </strong>{{ $day->date }}</td>
        </tr>
        <tr><td colspan="8">&nbsp;</td></tr>
    </table>

        @foreach ($day->days_data as $dayData)
            <table width="100%">
                <tr>
                    <th colspan="8" class="border">{{ $dayData->area->standard_area->title }}</th>
                </tr>
                <tr><td colspan="8">&nbsp;</td></tr>
                <tr>
                    @foreach($dayData->data as $data)
                        <td width="12%" class="border text-center @if($loop->index == 0 || $loop->index % 2 === 0) bg-grey @endif">{{ $data['structure'] ?? 'n/a' }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($dayData->data as $data)
                        <td width="12%" class="border text-center @if($loop->index == 0 || $loop->index % 2 === 0) bg-grey @endif">{{ $data['material'] ?? 'n/a' }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($dayData->data as $data)
                        <td width="12%" class="border text-center @if($loop->index == 0 || $loop->index % 2 === 0) bg-grey @endif">{{ $data['value'] ?? 'n/a' }}</td>
                    @endforeach
                </tr>
                <tr><td colspan="8">&nbsp;</td></tr>
            </table>
        @endforeach
    @endforeach
</div>
