<template>
    <div :class="{'visible': isRunning, 'hidden': !isRunning}" class="wrapper">
        <i class="fa fa-3x fa-circle-o-notch fa-spin loader"></i>
    </div>
</template>

<script type="text/babel">
    export default {
        name: 'loader',
        props: {
            isRunning: {
                required: true,
                type: Boolean
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>
    .wrapper {
        position: fixed;
        left: 0;
        background: rgba(255, 255, 255, 0.6);
        height: 100%;
        width: 100%;
        z-index: 999;
    }

    .hidden {
        visibility: hidden;
    }

    .visible {
        visibility: visible;
    }

    .loader {
        position: absolute;
        top: 49vh;
        right: 49%;
        color: grey;
    }
</style>
