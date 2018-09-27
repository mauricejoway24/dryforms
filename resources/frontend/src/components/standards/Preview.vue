<template>
    <b-modal id="selectForm" title="Select Forms for Preview" v-model="showModal">
        <b-form-checkbox-group stacked v-model="selectedForm" :options="forms"></b-form-checkbox-group>
        <div slot="modal-footer" class="w-100">
            <b-btn variant="primary" class="float-right" @click="saveForm()">Preview</b-btn>
        </div>
    </b-modal>
</template>

<script type="text/babel">
    export default {
        data() {
            return {
                showModal: true,
                selectedForm: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                selectedRecipients: [],
                forms: [
                    {text: 'Call Report', value: 1},
                    {text: 'Project Scope', value: 2},
                    {text: 'Daily Log', value: 3},
                    {text: 'Work Authorization', value: 4},
                    {text: 'Customer Responsibility', value: 5},
                    {text: 'Anti-Microbial', value: 6},
                    {text: 'Moisture Map', value: 7},
                    {text: 'Psychometric Report', value: 8},
                    {text: 'Release from Liability', value: 9},
                    {text: 'Work Stoppage', value: 10},
                    {text: 'Certificate of Completion', value: 11}
                ]
            }
        },
        created() {
        },
        watch: {
            showModal: function () {
                if (!this.showModal) this.$router.go(-1)
            }
        },
        methods: {
            saveForm() {
                var form = document.createElement('form')
                form.setAttribute('method', 'get')
                // form.setAttribute('action', '/form/preview/' + this.$session.get('apiToken'))
                form.setAttribute('action', '/form/preview/' + this.$localStorage.get('apiToken'))
                this.selectedForm.forEach(function (formId, key) {
                    var hiddenField = document.createElement('input')
                    hiddenField.setAttribute('type', 'hidden')
                    hiddenField.setAttribute('name', 'form' + key)
                    hiddenField.setAttribute('value', formId)
                    form.appendChild(hiddenField)
                })
                document.body.appendChild(form)
                form.submit()
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    .custom-control.custom-checkbox {
        width: 100%;
    }
</style>