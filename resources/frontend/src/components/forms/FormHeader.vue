<template>
    <b-row v-if="companyDetails">
        <b-col class="text-left col-8 logo-preview">
            <img v-if="companyDetails && companyDetails.logo" :src="companyDetails.public_logo_path" alt="Company Logo" height="90">
            <img v-else :src="companyLogo" alt="Company Logo" height="90">
        </b-col>
        <b-col class="text-right col-4">
            <h5 class="m-0">{{ companyDetails.name ? companyDetails.name : '' }}</h5>
            <p class="m-0">{{ companyDetails.street ? companyDetails.street : '' }}</p>
            <p class="m-0">{{ companyDetails.city ? companyDetails.city : '' }} {{ companyDetails.state ? companyDetails.state : '' }} {{ companyDetails.zip ? companyDetails.zip : '' }}</p>
            <p class="m-0">{{ companyDetails.phone ? companyDetails.phone : '' }}</p>
            <p class="m-0">{{ companyDetails.email ? companyDetails.email : '' }}</p>
        </b-col>
    </b-row>
</template>

<script type="text/babel">
    import {mapGetters} from 'vuex'

    export default {
        name: 'form-header',
        data() {
            return {
                companyLogo: require('../../assets/fallback-logo.jpg')
            }
        },
        computed: {
            ...mapGetters([
                'companyDetails'
            ])
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    .logo-preview img {
        height: auto;
        max-height: 100px;
    }
</style>
