<html>
<head>
    <link rel="stylesheet" type="text/css" href="{{ asset('app/static/vendor/bootstrap.css') }}">
    <style type="text/css">
        header {
            margin-top: 2000px;
        }
        .text-right {
            text-align: right;
        }
        td {
            font-size: 14px;
        }
        .border-bottom {
            border-bottom: 1px solid #ced4da;
        }
    </style>
</head>

<header>
    <table width="100%">
        <tr>
            <td width="30%"><img height="100px" src="{{ asset($company->public_logo_path) }}"></td>
            <td width="30%"></td>
            <td width="40%">
                <table width="100%">
                    <tr><td class="text-right">{{ $company->name }}</td></tr>
                    <tr><td class="text-right">{{ $company->street }}</td></tr>
                    <tr><td class="text-right">{{ $company->city }} {{ $company->state }} {{ $company->zip }}</td></tr>
                    <tr><td class="text-right">{{ $company->phone }}</td></tr>
                    <tr><td class="text-right">{{ $company->email }}</td></tr>
                </table>
            </td>
        </tr>
    </table>
    @isset($form)
        @include('pdf.partials._form_banner', ['callReport' => $callReport, 'title' => $title, 'instrument' => $callReport->project->instrument])
    @endif
    <table>
        <tr><td colspan="3">&nbsp;</td></tr>
    </table>
</header>
