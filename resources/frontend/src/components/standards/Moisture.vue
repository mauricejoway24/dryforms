<template>
	<div class="standards-moisture">
		<div v-if="isLoaded" class="card text-center">
			<div class="card-header">
			    <h5>{{ $route.meta.title }}</h5>
			</div>
			<div class="card-body">
                <h6>
                    Structure Dropdown Management
                    <button class="btn btn-sm btn-info pull-right" @click="showRevertStructureConfirm()">Revert</button>
                </h6>
                <hr/>
                <b-row>
                    <div class="col-md-12">
                        <b-table ref="structuresTable" :items="structures" small striped hover  :fields="header" head-variant="" align-v="center">
                            <template slot="no" slot-scope="data">
                                {{ data.index + 1 }}
                            </template>
                            <template slot="actions" slot-scope="row">
                                <i class="fa fa-trash text-danger" @click="deleteStructure(row.item.id)"></i>
                            </template>
                        </b-table>
                    </div>
                    <div class="col-md-6">
                        <form data-vv-scope="form-structure">
                            <label>Add Structure:</label>
                            <b-input-group :size="template_size">
                                <input type="text" class="form-control form-control-sm" placeholder="Input title" v-model="newStructure" name="newStructure" :class="{'is-invalid': errors.has('newStructure')}" v-validate data-vv-rules="required">
                                <b-input-group-button>
                                    <b-btn :size="template_size" :variant="'primary'" @click='addStructure'>+</b-btn>
                                </b-input-group-button>
                            </b-input-group>
                        </form>
                    </div>
                </b-row>
                <h6 class="mt-5">
                    Material Dropdown Management
                    <button class="btn btn-sm btn-info pull-right" @click="showRevertMaterialConfirm()">Revert</button>
                </h6>
                <hr/>
                <b-row>
                    <div class="col-md-12">
                        <b-table ref="materialsTable" :items="materials" small striped hover  :fields="header" head-variant="" align-v="center">
                            <template slot="no" slot-scope="data">
                                {{ data.index + 1 }}
                            </template>
                            <template slot="actions" slot-scope="row">
                                <i class="fa fa-trash text-danger" @click="deleteMaterial(row.item.id)"></i>
                            </template>
                        </b-table>
                    </div>
                    <div class="col-md-6">
                        <form data-vv-scope="form-material">
                            <label>Add Material:</label>
                            <b-input-group :size="template_size">
                                <input type="text" class="form-control form-control-sm" placeholder="Input title" v-model="newMaterial" name="newMaterial" :class="{'is-invalid': errors.has('newMaterial')}" v-validate data-vv-rules="required">
                                <b-input-group-button>
                                    <b-btn :size="template_size" :variant="'primary'" @click='addMaterial'>+</b-btn>
                                </b-input-group-button>
                            </b-input-group>
                        </form>
                    </div>
                </b-row>
			</div>
		</div>
        <loading v-else></loading>
        <b-modal id="revertStu" title="Revert Structure" class="text-left" @ok="revertStu()" v-model="showRevertStu"
                :ok-title="'Confirm'" :ok-variant="'danger'">
            <h5>Are you sure you want to revert to the default structures?</h5>
        </b-modal>
        <b-modal id="revertMat" title="Revert Material" class="text-left" @ok="revertMat()" v-model="showRevertMat"
                :ok-title="'Confirm'" :ok-variant="'danger'">
            <h5>Are you sure you want to revert to the default materials?</h5>
        </b-modal>
	</div>
</template>

<script type="text/babel">
import apiStandardStructure from '../../api/standard_structures'
import apiStandardMaterials from '../../api/standard_materials'
import ErrorHandler from '../../mixins/error-handler'
import Loading from '../layout/Loading'

export default {
    mixins: [ErrorHandler],
    components: {
        Loading
    },
    data() {
        return {
            header: {
                no: {
                    label: 'No',
                    'class': 'text-center',
                    sortable: false
                },
                title: {
                    label: 'Title',
                    sortable: true,
                    'class': 'text-center'
                },
                actions: {
                    label: 'Actions',
                    sortable: false,
                    'class': 'text-center'
                }
            },
            structures: [],
            materials: [],
            isLoaded: false,
            newStructure: '',
            newMaterial: '',
            showRevertStu: false,
            showRevertMat: false
        }
    },
    created() {
        let self = this
        const apis = [
            apiStandardStructure.index(),
            apiStandardMaterials.index()
        ]
        return Promise.all(apis)
            .then(response => {
                self.structures = response[0].data.structures
                self.materials = response[1].data.materials
                self.isLoaded = true
            })
            .catch(this.handleErrorResponse)
    },
    methods: {
        addStructure() {
            this.$validator.validateAll('form-structure')
            if (this.errors.has('newStructure')) {
                return
            }
            let self = this
            apiStandardStructure.store({
                title: this.newStructure
            })
            .then(function(response) {
                self.structures.push(response.data.structure)
                self.newStructure = ''
            })
            .catch(this.handleErrorResponse)
        },
        deleteStructure(id) {
            let self = this
            apiStandardStructure.delete(id)
            .then(function(response) {
                self.structures.splice(self.structures.findIndex(function(structure) {
                    return structure.id === id
                }), 1)
            })
            .catch(this.handleErrorResponse)
        },
        addMaterial() {
            this.$validator.validateAll('form-material')
            if (this.errors.has('newMaterial')) {
                return
            }
            let self = this
            apiStandardMaterials.store({
                title: this.newMaterial
            })
            .then(function(response) {
                self.materials.push(response.data.material)
                self.newMaterial = ''
            })
            .catch(this.handleErrorResponse)
        },
        deleteMaterial(id) {
            let self = this
            apiStandardMaterials.delete(id)
                .then(function(response) {
                    self.materials.splice(self.materials.findIndex(function(material) {
                        return material.id === id
                    }), 1)
                })
                .catch(this.handleErrorResponse)
        },
        showRevertStructureConfirm() {
            this.showRevertStu = true
        },
        showRevertMaterialConfirm() {
            this.showRevertMat = true
        },
        revertStu() {
            apiStandardStructure.revert()
                .then((response) => {
                    this.structures = response.data.structures
                })
                .catch(this.handleErrorResponse)
        },
        revertMat() {
            apiStandardMaterials.revert()
                .then((response) => {
                    this.materials = response.data.materials
                })
                .catch(this.handleErrorResponse)
        }
    }
}
</script>
