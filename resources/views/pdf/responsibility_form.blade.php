@include('pdf.partials._styles')

<div class="container">
    <table width="100%">
        @foreach($form->statements as $statement)
            @isset($statement->title)<tr><td> @if($statement->checked) <i class="fa fa-check-square"></i> @else <i class="fa fa-square-o"></i> @endif {{ $statement->title }}</td></tr>@endisset
            <tr>
                <td>{!! \App\Services\Statements\StatementTransformer::transformForPdf($callReport, $statement->statement) !!}</td>
            </tr>
        @endforeach
    </table>

    @if(!$form->project->equipment->isEmpty())
        <table width="100%">
            <tr>
                <td class="text-center border bg-grey"><strong>Category</strong></td>
                <td class="text-center border bg-grey"><strong>Make/Model</strong></td>
                <td class="text-center border bg-grey"><strong>Equipment #</strong></td>
                <td class="text-center border bg-grey"><strong>Crew/Team</strong></td>
                <td class="text-center border bg-grey"><strong>Set Date</strong></td>
                <td class="text-center border bg-grey"><strong>Status</strong></td>
            </tr>
            @foreach($form->project->equipment as $equipment)
                <tr>
                    <td class="text-center border">{{ $equipment->model && $equipment->model->category ? $equipment->model->category->name : 'n/a' }}</td>
                    <td class="text-center border">{{ $equipment->model ? $equipment->model->name : 'n/a' }}</td>
                    <td class="text-center border">{{ $equipment->serial ?? 'n/a' }}</td>
                    <td class="text-center border">{{ $equipment->team ? $equipment->team->name : 'n/a' }}</td>
                    <td class="text-center border">{{ $equipment->updated_at ?? 'n/a' }}</td>
                    <td class="text-center border">{{ $equipment->status ? $equipment->status->name : 'n/a' }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    @if($form->notes)
        <table width="100%">
            <tr>
                <td>
                    <strong>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $form->notes->updated_at)->format('m/d/Y H:i') }}
                        by {{ $form->notes->user ? $form->notes->user->full_name : 'n/a' }}</strong></td>
            </tr>
            <tr>
                <td>{{ $form->notes->notes }}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
        </table>
    @endif
</div>
