export default {
    name: 'data-process-mixin',
    data() {
        return {
            isRunning: false
        }
    },
    created() {
        this.$on('data-ready', () => {
            this.isRunning = false
        })
        this.$on('data-failed', () => {
            this.isRunning = false
        })
    },
    methods: {
        run() {
            this.isRunning = true
        }
    }
}
