@include('pdf.partials._styles')

<div class="container">
    <table width="100%">
        <tr>
            <td colspan="3" class="text-center border-bottom" width="100%"><h4 class="text-center">{{ $title ?? '&nbsp;' }}</h4></td>
        </tr>
        <tr><td class="2">&nbsp;</td></tr>
        <tr class="text-center"><td width="40%"><strong>Job Site</strong></td><td width="20%"></td><td width="40%"><strong>Owner/Insured Information</strong></td></tr>

        <tr><td class="2">&nbsp;</td></tr>
        <tr>
            <td width="40%">
                <table width="100%">
                    <tr>
                        <td width="50%">@if($callReport->is_residential) <i class="fa fa-check-square"></i> @else <i class="fa fa-square-o"></i> @endif Residential</td>
                        <td width="50%">@if($callReport->is_commercial) <i class="fa fa-check-square"></i> @else <i class="fa fa-square-o"></i> @endif Commercial</td>
                    </tr>
                </table>
            </td>
            <td width="20%"></td><td width="40%"><strong>Owner/Insured Name:</strong></td>
        </tr>
        <tr>
            <td width="40%">
                <table width="100%">
                    <tr>
                        <td width="50%">@if($callReport->is_insured) <i class="fa fa-check-square"></i> @else <i class="fa fa-square-o"></i> @endif Owner/Insured</td>
                        <td width="50%">@if($callReport->is_tenant) <i class="fa fa-check-square"></i> @else <i class="fa fa-square-o"></i> @endif Tenanat</td>
                    </tr>
                </table>
            </td>
            <td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insured_name ?? '&nbsp;' }}</td>
        </tr>

        <tr><td width="40%"></td><td width="20%"></td><td width="40%"><strong>Billing Address:</strong></td></tr>
        <tr><td width="40%"><strong>Contact Name:</strong></td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->billing_address ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%" class="border-input">{{ $callReport->contact_name ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%"><strong>City:</strong></td></tr>
        <tr><td width="40%"><strong>Contact Phone:</strong></td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insured_city ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%" class="border-input">{{ $callReport->contact_phone ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%"><strong>State:</strong></td></tr>
        <tr><td width="40%"><strong>Site Phone:</strong></td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insured_state ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%" class="border-input">{{ $callReport->site_phone ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%"><strong>Zip Code:</strong></td></tr>
        <tr><td width="40%"><strong>Date Contacted:</strong></td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insured_zip_code ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%" class="border-input">{{ $callReport->formatted_date_contacted ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%"><strong>Home Phone:</strong></td></tr>
        <tr><td width="40%"><strong>Date of Loss: </strong></td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insured_home_phone ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%" class="border-input">{{ $callReport->formatted_date_loss ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%"><strong>Cell Phone:</strong></td></tr>
        <tr><td width="40%"><strong>Point of Loss:</strong></td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insured_cell_phone ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%" class="border-input">{{ $callReport->point_loss ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%"><strong>Work Phone:</strong></td></tr>
        <tr><td width="40%"><strong>Date Completed:</strong></td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insured_work_phone ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%" class="border-input">{{ $callReport->formatted_date_completed ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%"><strong>Email:</strong></td></tr>
        <tr><td width="40%"></td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insured_email ?? '&nbsp;' }}</td></tr>

        <tr>
            <td width="40%">
                <table width="100%">
                    <tr>
                        <td width="50%">@if($callReport->is_water) <i class="fa fa-check-square"></i> @else <i class="fa fa-square-o"></i> @endif Water</td>
                        <td width="50%">@if($callReport->is_sewage) <i class="fa fa-check-square"></i> @else <i class="fa fa-square-o"></i> @endif Sewage</td>
                    </tr>
                </table>
            </td>
            <td width="20%"></td><td width="40%"><strong>Fax:</strong></td>
        </tr>

        <tr>
            <td width="40%">
                <table width="100%">
                    <tr>
                        <td width="50%">@if($callReport->is_mold) <i class="fa fa-check-square"></i> @else <i class="fa fa-square-o"></i> @endif Mold</td>
                        <td width="50%">@if($callReport->is_fire) <i class="fa fa-check-square"></i> @else <i class="fa fa-square-o"></i> @endif Fire</td>
                    </tr>
                </table>
            </td>
            <td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insured_fax ?? '&nbsp;' }}</td>
        </tr>

        <tr><td class="2">&nbsp;</td></tr>

        <tr><td width="40%"><strong>Category:</strong></td><td width="20%"></td><td width="40%" rowspan="2" class="text-center"><strong>Insurance Information</strong></td></tr>
        <tr><td width="40%" class="border-input">{{ $callReport->category ?? '&nbsp;' }}</td><td width="20%"></td></tr>

        <tr><td width="40%"><strong>Class:</strong></td><td width="20%"></td><td width="40%"><strong>Claim #:</strong></td></tr>
        <tr><td width="40%" class="border-input">{{ $callReport->class ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insurance_claim_no ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%"><strong>Job Address:</strong></td><td width="20%"></td><td width="40%"><strong>Insurance Company:</strong></td></tr>
        <tr><td width="40%" class="border-input">{{ $callReport->job_address ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insurance_company ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%"><strong>City:</strong></td><td width="20%"></td><td width="40%"><strong>Policy #:</strong></td></tr>
        <tr><td width="40%" class="border-input">{{ $callReport->city ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insurance_policy_no ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%"><strong>State:</strong></td><td width="20%"></td><td width="40%"><strong>Deductible:</strong></td></tr>
        <tr><td width="40%" class="border-input">{{ $callReport->state ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insurance_deductible ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%"><strong>Zip Code:</strong></td><td width="20%"></td><td width="40%"><strong>Insurance Adjuster:</strong></td></tr>
        <tr><td width="40%" class="border-input">{{ $callReport->zip_code ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insurance_adjuster ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%"><strong>Cross Streets:</strong></td><td width="20%"></td><td width="40%"><strong>Address:</strong></td></tr>
        <tr><td width="40%" class="border-input">{{ $callReport->cross_streets ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insurance_address ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%"><strong>Apartment Name:</strong></td><td width="20%"></td><td width="40%"><strong>City:</strong></td></tr>
        <tr><td width="40%" class="border-input">{{ $callReport->apartment_name ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insurance_city ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%"><strong>Building #:</strong></td><td width="20%"></td><td width="40%"><strong>State:</strong></td></tr>
        <tr><td width="40%" class="border-input">{{ $callReport->building_no ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insurance_state ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%"><strong>Unit #:</strong></td><td width="20%"></td><td width="40%"><strong>Zip Code:</strong></td></tr>
        <tr><td width="40%" class="border-input">{{ $callReport->apartment_no ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insurance_zip_code ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%"><strong>Gate Code:</strong></td><td width="20%"></td><td width="40%"><strong>Work Phone:</strong></td></tr>
        <tr><td width="40%" class="border-input">{{ $callReport->gate_code ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insurance_work_phone ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%"><strong>Assigned to:</strong></td><td width="20%"></td><td width="40%"><strong>Cell Phone:</strong></td></tr>
        <tr><td width="40%" class="border-input">{{ $callReport->assignee_name ?? '&nbsp;' }}</td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insurance_cell_phone ?? '&nbsp;' }}</td></tr>


        <tr><td width="40%"></td><td width="20%"></td><td width="40%"><strong>Email:</strong></td></tr>
        <tr><td width="40%"></td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insurance_email ?? '&nbsp;' }}</td></tr>

        <tr><td width="40%"></td><td width="20%"></td><td width="40%"><strong>Fax:</strong></td></tr>
        <tr><td width="40%"></td><td width="20%"></td><td width="40%" class="border-input">{{ $callReport->insurance_fax ?? '&nbsp;' }}</td></tr>
    </table>
</div>
