<template>
    <b-navbar toggleable="md" class="p-0 m-0">
        <b-navbar-toggle target="nav_collapse"></b-navbar-toggle>
        <b-navbar-brand class="text-center m-0 w-16" @click="$router.push('/')">
            <img :src="logoImage" alt="Logo" class="img-fluid">
        </b-navbar-brand>
        <b-collapse is-nav id="nav_collapse">
            <b-navbar-nav class="left-nav">
                <template v-if="authorized">
                    <b-nav-item v-for="item in menuItems" :key="item.route" :to="item.route" class="text-center">
                        <img :src="item.img" alt="Logo" class="img-fluid">
                        <p class="text-white font-weight-bold text-uppercase mb-0">{{ item.name }}</p>
                    </b-nav-item>
                </template>
            </b-navbar-nav>
            <b-navbar-nav class="right-nav ml-auto">
                <b-nav-item v-if="!authorized" to="/login" class="font-weight-bold mt-4">Login</b-nav-item>
                <b-nav-item v-if="!authorized" to="/register" class="font-weight-bold mt-4 mr-3">Register</b-nav-item>
                <b-nav-item v-if="authorized" class="dashboard text-center p-2" @click="gotoAdmin">
                    <img :src="dashboardImage">
                </b-nav-item>
                <b-nav-item v-if="authorized" v-on:click="logout()" class="logout w-16 mt-4">
                    <div class="text-white font-weight-bold">LOGOUT&nbsp;<i class="fa fa-sign-out"></i></div>
                </b-nav-item>
            </b-navbar-nav>
        </b-collapse>
        <div class="text-right text-white version">Version 1.00</div>
    </b-navbar>
</template>

<script type="text/babel">
    import authorization from '../../mixins/authorization'

    export default {
        mixins: [authorization],
        data() {
            return {
                logoImage: require('../../assets/logo.png'),
                dashboardImage: require('../../assets/dashboard.png'),
                logoutImage: require('../../assets/logout.png'),
                authorized: false,
                menuItems: [
                    {
                        name: 'Projects',
                        route: '/projects',
                        img: require('../../assets/nav-project.png')
                    },
                    {
                        name: 'Settings',
                        route: '/settings',
                        img: require('../../assets/nav-setting.png')
                    },
                    {
                        name: 'Standards',
                        route: '/standards',
                        img: require('../../assets/nav-standard.png')
                    },
                    {
                        name: 'Equipment',
                        route: '/equipment',
                        img: require('../../assets/nav-inventory.png')
                    },
                    {
                        name: 'Training',
                        route: '/training',
                        img: require('../../assets/nav-training.png')
                    }
                ]
            }
        },
        created() {
            this.authorized = this.$localStorage.get('apiToken')
        },
        watch: {
            '$route'(to, from) {
                this.authorized = this.$localStorage.get('apiToken')
            }
        },
        methods: {
            gotoAdmin() {
                window.location.href = '/admin'
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>
    $break-extra: 1200px;
    $nav-bg-dark: #515763;
    // $side-bg-dark: rgba(0, 0, 0, 0.3);
    $side-bg-dark: #0d1722;
    $left-side-width: 16.666667%;

    .navbar {
        background-color: $side-bg-dark;
        #nav_collapse {
            background-color: $nav-bg-dark;
            .left-nav {
                .nav-item {
                    border-left: 1px solid #898c93;
                    &:hover, .active {
                        background: linear-gradient(rgba(255, 255, 255, 0), rgba(17, 103, 179, 1));
                    }
                    @media screen and (max-width: $break-extra) {
                        font-size: 12px;
                        img {
                            height: 35px;
                        }
                    }
                }
            }
            .right-nav {
                height: 87px;
                .dashboard {
                    background-color: #046ac3;
                    position: absolute;
                    right: $left-side-width;
                    height: inherit;
                }
                .fa-sign-out {
                    display: initial;
                }
                .logout {
                    position: fixed;
                    right: -1%;
                }
                .nav-link {
                    color: white;
                    &:hover {
                        color: #ddd;
                    }
                }
                .active {
                    color: #ddd;
                }
                @media screen and (max-width: $break-extra) {
                    height: 69px;
                }
            }
        }
        .version {
            background-color: #4a4c52;
            position: fixed;
            top: 0;
            right: 0;
            width: $left-side-width;
        }
    }

    .w-16 {
        width: $left-side-width;
    }
</style>
