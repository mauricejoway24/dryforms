/* eslint-disable */
$.FroalaEditor.RegisterCommand('Tokens', {
    title: 'Tokens',
    type: 'dropdown',
    focus: false,
    undo: false,
    refreshAfterCallback: true,
    options: {
        '%apartment #%': 'Apartment #',
        '%apartment name%': 'Apartment Name',
        '%assigned to%': 'Assigned To',
        '%building #%': 'Building #',
        '%category%': 'Category',
        '%claim #%': 'Claim #',
        '%class%': 'Class',
        '%company%': 'Company',
        '%company phone%': 'Company Phone',
        '%contact name%': 'Contact Name',
        '%cross streets%': 'Cross Streets',
        '%date completed%': 'Date Completed',
        '%date contacted%': 'Date Contacted',
        '%date of loss%': 'Date of Loss',
        '%deductible%': 'Deductible',
        '%gate code%': 'Gate Code',
        '%insurance adjuster%': 'Insurance Adjuster',
        '%insurance company%': 'Insurance Company',
        '%job address%': 'Job Address',
        '%owner/insured name%': 'Owner/Insured Name',
        '%policy #%': 'Policy #',
        '%point of loss%': 'Point of Loss',
        '%time contacted%': 'Time Contacted'
    },
    callback: function (cmd, val) {
        this.html.insert(val)
    }
})
