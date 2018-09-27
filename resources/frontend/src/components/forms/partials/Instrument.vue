<template>
    <b-row v-if="showInstrument">
        <b-col cols="6" class="text-left">
            <p>Instrument Make: <input type="text" v-model="instrument.make" @input="save()" placeholder="n/a"></p>
        </b-col>
        <b-col cols="6" class="text-left">
            <p>Instrument Model: <input type="text" v-model="instrument.model" @input="save()" placeholder="n/a"></p>
        </b-col>
    </b-row>
</template>

<script>
    import apiInstruments from '../../../api/project_instruments'
    import _ from 'lodash'

    export default {
        name: 'instrument',
        props: {
            showInstrument: {
                type: Boolean,
                default() {
                    return false
                }
            },
            projectId: {
                required: true
            },
            instrument: {
                type: Object,
                default() {
                    return {
                        make: null,
                        model: null
                    }
                }
            }
        },
        methods: {
            save: _.debounce(function () {
                apiInstruments.update(this.projectId, this.instrument)
                    .then(response => {
                }).catch(this.handleErrorResponse)
            }, 500)
        }
    }
</script>

<style scoped>
    input {
        border: none;
    }
</style>
