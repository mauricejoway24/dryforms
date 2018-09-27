export default {
  data: function() {
    return {
      get template_size() {
        return 'sm'
      },
      get pageSizeOption() {
        return [
          {
            text: 5,
            value: 5
          },
          {
            text: 10,
            value: 10
          },
          {
            text: 15,
            value: 15
          },
          {
            text: 20,
            value: 20
          }
        ]
      },
      get defLen() {
        return 50
      },
      get logoRootPath() {
        return '/storage/'
      }
    }
  },
  methods: {
    browserDetect: function() {
      if (/Android/.test(navigator.userAgent)) return 'Android'
      if (/Edge/.test(navigator.userAgent)) return 'Edge'
      if (/Firefox/.test(navigator.userAgent)) return 'Firefox'
      if (/Google Inc/.test(navigator.vendor)) return 'Google Chrome'
      if (/CriOS/.test(navigator.userAgent)) return 'Chrome IOS'
      if (!!window.chrome && !/Edge/.test(navigator.userAgent)) return 'ChromiumBase'
      if (/Trident/.test(navigator.userAgent)) return 'IE'
      if (/(iPhone|iPad|iPod)/.test(navigator.platform)) return 'IOS'
      if (/OPR/.test(navigator.userAgent)) return 'Opera'
      if (/Safari/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent)) return 'Safari'
    }
  }
}
