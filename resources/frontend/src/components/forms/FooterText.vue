<template>
    <b-row>
        <b-col v-if="footer.visible" v-html="footer.content" class="text-center"></b-col>
    </b-row>
</template>

<script type="text/babel">
    export default {
        name: 'footer-text',
        data() {
            return {}
        },
        props: {
            footer: {
                type: Object,
                required: false,
                default() {
                    return {
                        visible: false,
                        content: null
                    }
                }
            }
        }
    }
</script>