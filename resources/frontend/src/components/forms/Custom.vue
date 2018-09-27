<template>
    <div class="card" style="min-height: 100vh;">
        <loader :is-running="isRunning"></loader>

        <div class="card-body text-center">
            <form-header></form-header>
            <h4 class="mb-2">{{ form_title }}</h4>
            <div class="dropdown-divider"></div>
            <form-banner class="mt-2"></form-banner>
            <div class="dropdown-divider"></div>
            <statement :statements="form.statements"></statement>
            <notes class="mt-3" :show-additional-notes="form.additional_notes_show"></notes>
            <footer-text :footer="form.footer" class="mt-3"></footer-text>
            <signature class="mt-3" :form="form"></signature>
        </div>
    </div>
</template>

<script type="text/babel">
    import FormHeader from './FormHeader'
    import FormBanner from './FormBanner'
    import Notes from './Notes'
    import FooterText from './FooterText'
    import Signature from './Signature'
    import Statement from './Statement'
    import ErrorHandler from '../../mixins/error-handler'
    import apiProjectForms from '../../api/project_forms'
    import Loader from '../layout/Loader'
    import DataProcessMixin from '../../mixins/data-process'

    export default {
        mixins: [ErrorHandler, DataProcessMixin],
        name: 'project custom',
        components: {
            FormHeader,
            FormBanner,
            Notes,
            FooterText,
            Signature,
            Statement,
            Loader
        },
        data() {
            return {
                form: {},
                form_id: null,
                form_title: null
            }
        },
        created() {
            this.$nextTick(() => {
                this.init()
            })
        },
        methods: {
            init() {
                this.run()
                apiProjectForms.show(this.$route.params.project_id, {
                    id: this.$route.params.form_id
                }).then(response => {
                    this.form = response.data.form
                    this.$emit('data-ready')
                }).catch(response => {
                    this.$emit('data-failed')
                    this.handleErrorResponse(response)
                })
            }
        }
    }
</script>
