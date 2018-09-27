<html>
<head>
    <style>
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        td {
            font-size: 14px;
        }
    </style>
</head>
<body>

@if($form && ($form->insured_signature || $form->company_signature))
    <table width="100%">
        <tr>
            <td width="30%"><strong>Owner/Insured:</strong></td>
            <td width="40%" rowspan="2" class="text-center"><img height="50" src="{{ $form->insured_signature }}"></td>
            <td width="30%" rowspan="2" class="text-right">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $form->insured_signature_upated_at)->format('m/d/Y H:i') }}</td>
        </tr>
        <tr>
            <td width="30%">{{ $callReport->insured_name }}</td>
            <td width="40%"></td>
            <td width="30%"></td>
        </tr>

        <tr>
            <td width="30%"><strong>Company:</strong></td>
            <td width="40%" rowspan="2" class="text-center"><img height="50" src="{{ $form->company_signature }}"></td>
            <td width="30%" rowspan="2" class="text-right">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $form->company_signature_upated_at)->format('m/d/Y H:i') }}</td>
        </tr>
        <tr>
            <td width="30%">{{ $form->company->name }}</td>
            <td width="40%"></td>
            <td width="30%"></td>
        </tr>
    </table>
@endif

@if($form && $form->footer_text_show)
    <table width="100%">
        <tr>
            <td>{!! $form->footer_text !!}</td>
        </tr>
    </table>
@endif

</body>
</html>
