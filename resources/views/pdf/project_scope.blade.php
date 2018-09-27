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
    .page-break {
        page-break-after: always;
    }
</style>

<div class="container">
    @foreach($form->project->areas as $area)
        <table width="100%">
            <tr class="border">
                <td colspan="8" class="text-center bg-grey border"><strong>{{ $area->standard_area->title }}</strong></td>
            </tr>
            <tr>
                <td colspan="4" class="text-center bg-grey border" width="50%"><strong>Overall Square Feet</strong></td>
                <td colspan="4" class="text-center border" width="50%">{{ $area->overal_square_feet }}</td>
            </tr>
            <tr><td colspan="8">&nbsp;</td></tr>
        </table>

        <table>
            @foreach($area->scopes as $scope)
                <tr>
                    @if($scope->is_header)
                        <td width="5%" class="text-center border bg-grey">
                            <i class="fa fa-times"></i>
                        </td>
                        <td width="35%" class="text-center border bg-grey">{{ $scope->service }}</td>
                        <td width="5%" class="text-center border bg-grey">UOM</td>
                        <td width="5%" class="text-center border bg-grey">QTY</td>
                    @else
                        <td width="5%" class="text-center border">
                            @if($scope->selected)
                                <i class="fa fa-check"></i> @else &nbsp;
                            @endif
                        </td>
                        <td width="35%" class="text-center border">{{ $scope->service }}</td>
                        <td width="5%" class="text-center border">{{ $scope->uom_info ? $scope->uom_info->title : '&nbsp;' }}</td>
                        <td width="5%" class="text-center border">{{ $scope->qty }}</td>
                    @endif
                    <td width="4%">&nbsp;</td>
                    @if($area->scopes[$loop->index + ($loop->count / 2)]->is_header)
                        <td width="5%" class="text-center border bg-grey">
                            <i class="fa fa-times"></i>
                        </td>
                        <td width="35%" class="text-center border bg-grey">{{ $scope->service }}</td>
                        <td width="5%" class="text-center border bg-grey">UOM</td>
                        <td width="5%" class="text-center border bg-grey">QTY</td>
                    @else
                        <td width="5%" class="text-center border">
                            @if($area->scopes[$loop->index + ($loop->count / 2)]->selected)
                                <i class="fa fa-check"></i> @else &nbsp;
                            @endif
                        </td>
                        <td width="35%" class="text-center border">{{ $area->scopes[$loop->index + ($loop->count / 2)]->service }}</td>
                        <td width="5%" class="text-center border">{{ $area->scopes[$loop->index + ($loop->count / 2)]->uom_info ? $area->scopes[$loop->index + ($loop->count / 2)]->uom_info->title : '&nbsp;' }}</td>
                        <td width="5%" class="text-center border">{{ $area->scopes[$loop->index + ($loop->count / 2)]->qty }}</td>
                    @endif
                </tr>
                @break($loop->count / 2 === ($loop->index + 1))
            @endforeach
            <tr>
                <td colspan="4">&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="4">&nbsp;</td>
            </tr>
        </table>
        <div class="page-break"></div>
    @endforeach

    <table width="100%">
        <tr class="border">
            <td colspan="8" class="text-center bg-grey border"><strong>Miscellaneous</strong></td>
        </tr>
        <tr><td colspan="8">&nbsp;</td></tr>
    </table>
    <table>
        @foreach($miscScopes as $scope)
            <tr>
                @if($scope->is_header)
                    <td width="5%" class="text-center border bg-grey">
                        <i class="fa fa-times"></i>
                    </td>
                    <td width="35%" class="text-center border bg-grey">{{ $scope->service }}</td>
                    <td width="5%" class="text-center border bg-grey">UOM</td>
                    <td width="5%" class="text-center border bg-grey">QTY</td>
                @else
                    <td width="5%" class="text-center border">
                        @if($scope->selected)
                            <i class="fa fa-check"></i> @else &nbsp;
                        @endif
                    </td>
                    <td width="35%" class="text-center border">{{ $scope->service }}</td>
                    <td width="5%" class="text-center border">{{ $scope->uom_info ? $scope->uom_info->title : '&nbsp;' }}</td>
                    <td width="5%" class="text-center border">{{ $scope->qty }}</td>
                @endif
                <td width="4%">&nbsp;</td>
                @if($area->scopes[$loop->index + ($loop->count / 2)]->is_header)
                    <td width="5%" class="text-center border bg-grey">
                        <i class="fa fa-times"></i>
                    </td>
                    <td width="35%" class="text-center border bg-grey">{{ $scope->service }}</td>
                    <td width="5%" class="text-center border bg-grey">UOM</td>
                    <td width="5%" class="text-center border bg-grey">QTY</td>
                @else
                    <td width="5%" class="text-center border">
                        @if($area->scopes[$loop->index + ($loop->count / 2)]->selected)
                            <i class="fa fa-check"></i> @else &nbsp;
                        @endif
                    </td>
                    <td width="35%" class="text-center border">{{ $area->scopes[$loop->index + ($loop->count / 2)]->service }}</td>
                    <td width="5%" class="text-center border">{{ $area->scopes[$loop->index + ($loop->count / 2)]->uom_info ? $area->scopes[$loop->index + ($loop->count / 2)]->uom_info->title : '&nbsp;' }}</td>
                    <td width="5%" class="text-center border">{{ $area->scopes[$loop->index + ($loop->count / 2)]->qty }}</td>
                @endif
            </tr>
            @break($loop->count / 2 === ($loop->index + 1))
        @endforeach
        <tr>
            <td colspan="4">&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="4">&nbsp;</td>
        </tr>
    </table>
    <div class="page-break"></div>
</div>
