<template>
    <b-modal id="calculator" title="Calculator" class="text-left" @ok="store" v-model="show">
        <div style="width:100%;">
            <div style="width:30%; float:left">
                <b-form-input size="sm" type="number" v-model="height" placeholder="H"></b-form-input>
            </div>
            <div style="width:5%; float:left; text-align: center;">
                <label> * </label>
            </div>
            <div style="width:30%; float:left">
                <b-form-input size="sm" type="number" v-model="width" placeholder="W"></b-form-input>
            </div>
            <div style="width:5%; float:left; text-align: center;">
                <label> = </label>
            </div>
            <div style="width:30%; float:left">                
                <b-form-input size="sm" type="number" :value="width*height" placeholder=""></b-form-input>
            </div>            
        </div>
    </b-modal>
</template>

<script type="text/babel">
    import ErrorHandler from '../../../mixins/error-handler'

    export default {
        mixins: [ErrorHandler],
        name: 'calculator',
        created() {
            this.$parent.$on('openCalculatorModal', (areaIndex) => {
                this.area_index = areaIndex
                this.width = 0
                this.height = 0
                this.show = true
            })
        },
        data() {
            return {
                show: false,
                area_index: 0,
                width: 0,
                height: 0
            }
        },
        methods: {
            store: function() {
                this.$parent.$emit('changeOSF', {
                    area_index: this.area_index,
                    val: this.width * this.height
                })
                this.show = false
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
</style>