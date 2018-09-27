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
