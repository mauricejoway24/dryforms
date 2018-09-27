<template>
    <div class="card" style="min-height: 100vh;">
        <loader :is-running="isRunning"></loader>

        <div class="card-body text-center">
            <form-header></form-header>
            <h4 class="mb-2">{{ form.title }}</h4>
            <div class="dropdown-divider"></div>
            <form-banner class="mt-2"></form-banner>
            <div class="dropdown-divider"></div>
            <statement :statements="form.statements" :show-checkboxes="true"></statement>
            <notes class="mt-3" :show-additional-notes="form.additional_notes_show"></notes>
            <footer-text :footer="form.footer" class="mt-3"></footer-text>
            <signature class="mt-3" :form="form"></signature>
        </div>
    </div>
</template>

<script type="text/babel">
    import BaseForm from './_base/BaseForm'

    export default {
        extends: BaseForm,
        name: 'anti-microbial'
    }
</script>
