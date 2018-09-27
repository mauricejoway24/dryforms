<div class="container">
    <table width="100%">
        <tr>
            <td colspan="3" class="text-center border-bottom" width="100%">
                <h4 class="text-center" style="text-align: center; font-size: 21px; font-weight: 200;">{{ $title }}</h4>
            </td>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <td width="60%"><strong>Owner/Insured:</strong> {{ $callReport->insured_name }}</td>
            <td width="5%"></td>
            <td width="35%" class="text-right"><strong>Claim #:</strong> {{ $callReport->insurance_claim_no }}</td>
        </tr>
        <tr>
            <td width="60%"><strong>Job Address:</strong> {{ $callReport->full_job_address }}</td>
            <td width="5%"></td>
            <td width="35%"></td>
        </tr>
        @if(isset($instrument))
            <tr>
                <td width="60%"><strong>Instrument Make:</strong> {{ $instrument->make }}</td>
                <td width="5%"></td>
                <td width="35%" class="text-right"><strong>Instrument Model:</strong> {{ $instrument->model }}</td>
            </tr>
        @endif
    </table>
    <hr>
</div>
