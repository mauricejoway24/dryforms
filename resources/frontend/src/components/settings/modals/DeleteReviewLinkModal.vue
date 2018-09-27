<template>

    <b-modal id="deleteReviewLink" :title="modalName" class="text-left" @ok="destroy()" v-model="show"
             :ok-title="'Confirm'" :ok-variant="'danger'">
        <h5>Are you sure?</h5>
    </b-modal>

</template>

<script type="text/babel">
    import apiReviewLinks from '../../../api/review_links'

    export default {
        name: 'delete-review-link-modal',
        created() {
            this.$parent.$on('openDeleteModal', (payload) => {
                this.reviewLink.id = payload.id
                this.show = true
            })
        },
        data() {
            return {
                show: false,
                reviewLink: {
                    id: null
                }
            }
        },
        computed: {
            modalName() {
                return 'Delete Review Link'
            }
        },
        methods: {
            destroy() {
                if (this.reviewLink.id) {
                    apiReviewLinks.delete(this.reviewLink.id)
                        .then(response => {
                            this.$parent.$emit('reloadData')
                        })
                        .catch(error => {
                            console.log(error.data)
                        })
                }
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
</style>