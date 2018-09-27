<template>

    <b-modal id="createReviewLink" :title="modalName" class="text-left" @ok="store" v-model="show">
        <div class="form-group">
            <label>Review Link:</label>
            <b-input-group>
                <b-form-input type="text" placeholder="Enter Link Url"  v-model="reviewLink.url" name="url" :class="{'is-invalid': errors.has('url')}" v-validate data-vv-rules="required"/>
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="checkbox" v-model="reviewLink.activate" true-value=1 false-value=0 :disabled='activateCount>=3 && reviewLink.activate==0' @change='watchActivateCount'>
                    </div>
                </div>
            </b-input-group>
        </div>
    </b-modal>

</template>

<script type="text/babel">
    import apiReviewLinks from '../../../api/review_links'

    export default {
        name: 'create-review-link-modal',
        created() {
            this.$parent.$on('openCreateModal', (payload) => {
                this.reviewLink = {
                    id: null,
                    url: null,
                    activate: 0
                }
                this.activateCount = payload.activateCount
                this.errors.clear()
                this.show = true
            })
            this.$parent.$on('openEditModal', (payload) => {
                this.reviewLink.id = payload.id
                this.activateCount = payload.activateCount
                this.initData()
                this.errors.clear()
                this.show = true
            })
        },
        data() {
            return {
                show: false,
                reviewLink: {
                    id: null,
                    url: null,
                    activate: 0
                },
                activateCount: 0
            }
        },
        computed: {
            modalName() {
                return this.reviewLink.id ? 'Edit Link' : 'Create Link'
            }
        },
        methods: {
            store(evt) {
                this.$validator.validateAll()
                if (this.errors.any()) {
                    evt.preventDefault()
                    return
                }

                if (!this.reviewLink.id) {
                    apiReviewLinks.store(this.reviewLink)
                        .then(response => {
                            this.$parent.$emit('reloadData')
                        })
                        .catch(error => {
                            console.log(error)
                        })
                } else {
                    apiReviewLinks.patch(this.reviewLink.id, this.reviewLink)
                        .then(response => {
                            this.$parent.$emit('reloadData')
                        })
                        .catch(error => {
                            console.log(error)
                        })
                }
            },
            initData() {
                let self = this
                apiReviewLinks.show(this.reviewLink.id)
                    .then(response => {
                        self.reviewLink = response.data
                    })
                    .catch(error => {
                        console.log(error)
                    })
            },
            watchActivateCount() {
                if (parseInt(this.reviewLink.activate) === 1) {
                    this.activateCount ++
                } else {
                    this.activateCount --
                }
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
</style>