<template>
    <b-list-group class="text-left p-0 left-side-bar" v-if='isLoaded'>
        <template v-if="isStandards">
            <b-list-group-item
                class="list-complete-item"
                :class="'Forms Order' === $route.name ? 'bg-blue mb-2' : 'bg-grey mb-2'">
                <router-link
                    :to="{name: 'Forms Order'}"
                    :class="'Forms Order' === $route.name ? 'pointer text-white' : 'pointer text-black'">
                    <div class="m-0">
                        <img v-if="leftLinksIcon['Forms Order']" :src="leftLinksIcon['Forms Order']"
                             class="left-sidebar-img" style="margin-top: 4px;">
                        <span class="left-sidebar-ellipse icon-margin">
                            Forms Order
                        </span>
                    </div>
                </router-link>
            </b-list-group-item>

            <b-list-group-item
                    v-for="link in formsOrder"
                    v-if="!linkShouldBeSkipped(link.name)"
                    :key="link.form_id"
                    class="list-complete-item"
                    :class="link.form_id === formId || getLink(link) === $route.path ? 'bg-blue' : 'bg-grey'">
                <router-link
                        :to="getLink(link)"
                        :class="link.form_id === formId || getLink(link) === $route.path ? 'pointer text-white' : 'pointer text-black'">
                    <div class="m-0">
                        <div class="left-sidebar-img" v-if="leftLinksIcon[link.form.name]">
                            <img :src="leftLinksIcon[link.form.name]"/>
                        </div>
                        <div class="left-sidebar-img" v-else>
                            <span>&nbsp;&nbsp;<i class="fa fa-user"></i>&nbsp;&nbsp;</span>
                        </div>
                        <span class="left-sidebar-ellipse" :class="leftLinksIcon[link.form.name] ? 'icon-margin' : ''">
                            {{ link.name }}
                        </span>
                    </div>
                </router-link>
            </b-list-group-item>
        </template>

        <template v-else-if="isForms">
            <b-list-group-item
                class="list-complete-item"
                :class="'Forms' === $route.name ? 'bg-blue mb-2' : 'bg-grey mb-2'">
                <router-link
                    :to="{name: 'Forms', params: {project_id: projectId}}"
                    :class="'Forms' === $route.name ? 'pointer text-white' : 'pointer text-black'">
                    <div class="m-0">
                        <img v-if="leftLinksIcon['Forms']" :src="leftLinksIcon['Forms']" class="left-sidebar-img"
                             style="margin-top: 4px;">
                        <span class="left-sidebar-ellipse icon-margin">
                            Forms
                        </span>
                    </div>
                </router-link>
            </b-list-group-item>

            <b-list-group-item
                v-for="link in projectSidebar" :key="link.id"
                class="list-complete-item"
                :class="link.form_id === formId ? 'bg-blue' : 'bg-grey'"
                v-if="link.form_id !== 12 && (link.selected === '1' || (projectSelectedForms && projectSelectedForms[link.form_id]))">
                <router-link
                    v-if="link.form_id >= 13"
                    :to="{name: 'Custom Form', params: {project_id: projectId, form_id: link.form_id}}"
                    :class="link.form_id === formId ? 'pointer text-white' : 'pointer text-black'">
                    <div class="m-0">
                        <span class="left-sidebar-ellipse"> <i class="fa fa-user"></i> &nbsp;&nbsp;&nbsp; {{ link.name }} </span>
                    </div>
                </router-link>
                <router-link
                    v-else
                    :to="{name: 'Form ' + link.standard_form.name, params: {project_id: projectId, form_id: link.form_id}}"
                    :class="link.form_id === formId ? 'pointer text-white' : 'pointer text-black'">
                    <div class="m-0">
                        <img v-if="leftLinksIcon[link.standard_form.name]" :src="leftLinksIcon[link.standard_form.name]"
                             class="left-sidebar-img">
                        <span class="left-sidebar-ellipse"> {{ link.name }} </span>
                    </div>
                </router-link>
            </b-list-group-item>
        </template>

        <template v-else-if="isTraining">
            <b-list-group-item
                v-for="link in trainingCategories"
                :key="link.id"
                class="list-complete-item"
                :class="$route.params.category_id === link.id ? 'bg-blue' : 'bg-grey'">
                <router-link
                    :to="{name: 'TrainingCategories', params: {category_id: link.id}}"
                    :class="$route.params.category_id === link.id ? 'pointer text-white' : 'pointer text-black'">
                    <div class="m-0">
                        <div class="left-sidebar-img" v-if="link.icon !== ''"><img :src="link.icon"/></div>
                        <span class="left-sidebar-ellipse"
                              :class="link.icon ? 'icon-margin' : ''"> {{ link.name }} </span>
                    </div>
                </router-link>
            </b-list-group-item>
        </template>

        <b-list-group-item
            v-for="link in leftLinks"
            v-if="!isLinkExistsInSidebar(link.name)"
            :key="link.name"
            class="list-complete-item"
            :class="link.name === $route.name ? 'bg-blue' : 'bg-grey'">
            <router-link
                :to="link.path"
                :class="link.name === $route.name ? 'pointer text-white' : 'pointer text-black'">
                <div class="m-0">
                    <div class="left-sidebar-img" v-if="link.icon !== ''">
                        <img :src="link.icon"/>
                    </div>
                    <span class="left-sidebar-ellipse" :class="link.icon ? 'icon-margin' : ''">
                        {{ link.name }}
                    </span>
                </div>
            </router-link>
        </b-list-group-item>
    </b-list-group>
    <loading v-else></loading>
</template>

<script type="text/babel">
    import {mapActions, mapGetters} from 'vuex'
    import Loading from './Loading'
    import apiStandardForm from '../../api/standard_form'

    import _ from 'lodash'
    import ErrorHandler from '../../mixins/error-handler'

    export default {
        mixins: [ErrorHandler],
        components: {
            Loading
        },
        created() {
            this.authorized = this.$localStorage.get('apiToken')
        },
        computed: {
            ...mapGetters([
                'projectSidebar',
                'formsOrder'
            ]),
            projectSelectedForms() {
                let projectSelectedForms = []
                this.$store.state.ProjectForm.projectForms.forEach(projectForm => {
                    projectSelectedForms[projectForm.form_id] = true
                })
                return projectSelectedForms
            },
            trainingCategories() {
                return this.$store.state.TrainingCategory.trainingCategories
            },
            isLoaded() {
                return this.isStandards === false || (this.isStandards === true && this.formsOrder.length !== 0)
            }
        },
        mounted() {
            this.leftLinksIcon = this.$config.get('leftLinksIcon')
            this.leftLinks = this.$route.meta.leftLinks
            this.fetchUser()
            if (this.$route.path.indexOf('standards') !== -1) {
                this.isStandards = true
                this.fetchFormsOrder()
            } else {
                this.isStandards = false
            }
            if (this.$route.path.indexOf('forms') !== -1) {
                this.fetchFormsOrder()
                this.isForms = true
            } else {
                this.isForms = false
            }
            if (this.$route.path.indexOf('training') !== -1) {
                this.isTraining = true
                this.fetchTrainingCategory()
            } else {
                this.isTraining = false
            }
        },
        data() {
            return {
                authorized: false,
                leftLinks: [],
                isStandards: null,
                isForms: null,
                isTraining: null,
                leftLinksIcon: {},
                projectId: null,
                formId: null
            }
        },
        methods: {
            ...mapActions([
                'fetchFormsOrder',
                'fetchProjectForm',
                'fetchTrainingCategory',
                'fetchUser',
                'fetchProjectSidebar'
            ]),
            updateFormName: _.debounce(function (standardForm) {
                if (standardForm.id) {
                    apiStandardForm.patch(standardForm.id, {
                        id: standardForm.id,
                        name: standardForm.name
                    }).then(response => {
                    }).catch(this.handleErrorResponse)
                } else {
                    apiStandardForm.store(standardForm)
                        .then(response => {
                            standardForm.id = response.data.form.id
                        }).catch(this.handleErrorResponse)
                }
            }, 500),
            getLink(link) {
                let linkData = {name: 'Standards Form', params: {form_id: link.form_id}}
                if (_.indexOf(['Affected Areas', 'Moisture Map'], link.name) !== -1) {
                    let urlData = _.find(this.leftLinks, function(url) { return url.name === link.name })
                    linkData = urlData.path
                }
                if (link.name === 'Project Scope') {
                    linkData = '/standards/scope/form_id/' + link.form_id
                }

                return linkData
            },
            isLinkExistsInSidebar(name) {
                return _.find(this.formsOrder, function(url) { return url.name === name })
            },
            linkShouldBeSkipped(name) {
                return _.indexOf(['Call Report', 'Daily Log', 'Psychometric Report'], name) !== -1
            }
        },
        watch: {
            '$route'(to, from) {
                this.leftLinks = to.meta.leftLinks
                if (to.path.indexOf('standards') !== -1) {
                    this.isStandards = true
                    this.formId = to.params.form_id
                    this.fetchFormsOrder()
                } else {
                    this.isStandards = false
                }
                if (to.path.indexOf('forms') !== -1) {
                    this.isForms = true
                    this.fetchProjectSidebar(to.params.project_id)
                    this.projectId = to.params.project_id
                    this.formId = to.params.form_id
                    this.$store.state.ProjectForm.projectId = this.projectId
                    this.fetchProjectForm()
                } else {
                    this.isForms = false
                }
                if (to.path.indexOf('training') !== -1) {
                    this.isTraining = true
                    this.fetchTrainingCategory()
                } else {
                    this.isTraining = false
                }
            },
            '$store.state.User.company'(company, previousCompany) {
                if (!company) {
                    this.$router.push({
                        name: 'Company',
                        params: {
                            user_id: this.$store.state.User.user.id
                        }
                    })
                }
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>
    .left-side-bar.list-group {
        height: calc(100vh - 69px);
        background-color: #0d1722;
        overflow: auto;
    }

    .list-group-item {
        margin-bottom: 3px;
        border-radius: unset;
        padding: 0.75rem 0.75rem;
    }

    .bg-blue {
        background-color: #046ac3;
    }

    .bg-grey {
        background-color: #c8c8c8;
    }

    .bg-side-left {
        background-color: rgba(187, 187, 187, 0.2);
        border-radius: 50px;
        box-shadow: inset 0px 0px 20px 2px rgba(230, 219, 219, 0.18), 0px 0px 0px 0px rgba(228, 219, 219, 0.15);
        margin: 15px 15px 0 15px;
        border: 0px;
    }

    .text-black {
        color: black;
    }

    .leftLinkInput {
        text-align: left;
        background-color: transparent;
        border: none;
        width: calc(100% - 39px);
        float: left;
        margin-left: 7px;
        cursor: pointer;
    }
</style>
