@include('pdf.partials._styles')

<style>
    table {
        page-break-after: avoid;
    }
</style>

<div class="container">
    @foreach($logs as $log)
        <table width="100%">
            <tr>
                <td><strong>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $log->updated_at)->format('m/d/Y H:i') }} by {{ $log->user ? $log->user->full_name : 'n/a' }}</strong></td>
            </tr>
            <tr>
                <td>{{ $log->notes }}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
        </table>
    @endforeach
</div>
