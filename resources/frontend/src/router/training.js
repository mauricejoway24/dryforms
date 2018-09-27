const leftLinks = []

const rightLinks = [
    {
        path: '/training/support/list',
        name: 'Suggestions/Support',
        icon: require('../assets/icon-support.png')
      },
    {
        path: '/training/calendar',
        name: 'Calendar',
        icon: require('../assets/icon-calendar.png')
    }
]

const routes = (configRoute) => [
    {
        path: '/training',
        name: 'Training First Page',
        props: { title: 'Training' },
        meta: {
            title: 'Training',
            roles: ['customer'],
            leftLinks: leftLinks,
            rightLinks: rightLinks
        },
        component: resolve => {
            require(['../components/training/Training.vue'], resolve)
        }
    },
    {
        path: '/training/category_id/:category_id',
        name: 'TrainingCategories',
        meta: {
            title: 'Training',
            roles: ['customer'],
            leftLinks: leftLinks,
            rightLinks: rightLinks
        },
        component: resolve => {
            require(['../components/training/Main.vue'], resolve)
        }
    },
    {
        path: '/training/calendar',
        name: 'Calendar',
        props: { title: 'Calendar' },
        meta: {
            title: 'Calendar',
            roles: ['customer'],
            leftLinks: leftLinks,
            rightLinks: rightLinks
        },
        component: resolve => {
            require(['../components/training/right-link/Calendar.vue'], resolve)
        }
    },
    {
        path: '/training/support/list',
        name: 'UserTickets',
        props: {title: 'My Tickets'},
        meta: {
          title: 'My Tickets',
          roles: ['customer'],
          leftLinks: leftLinks,
          rightLinks: rightLinks
        },
        component: resolve => {
          require(['../components/ticket/UserTickets.vue'], resolve)
        }
      },
      {
          path: '/training/support/ticket/:ticket_id',
          name: 'TicketInfo',
          props: {title: 'My Ticket'},
          meta: {
            title: 'My Ticket',
            roles: ['customer'],
            leftLinks: leftLinks,
            rightLinks: rightLinks
          },
          component: resolve => {
            require(['../components/ticket/TicketInfo.vue'], resolve)
          }
      },
      {
          path: '/training/support/create',
          name: 'CreateTicket',
          props: {title: 'Open New Ticket'},
          meta: {
            title: 'Open New Ticket',
            roles: ['customer'],
            leftLinks: leftLinks,
            rightLinks: rightLinks
          },
          component: resolve => {
            require(['../components/ticket/Create.vue'], resolve)
          }
      }
]

export default routes
