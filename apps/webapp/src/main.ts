import 'primevue/resources/themes/lara-light-blue/theme.css'
import 'primevue/resources/primevue.min.css'
import 'primeflex/primeflex.css'
import 'primeicons/primeicons.css'
import './global.css'

import PrimeVue from 'primevue/config'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

import VueApexCharts from 'vue3-apexcharts'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Checkbox from 'primevue/checkbox'
import Toast from 'primevue/toast'
import ToastService from 'primevue/toastservice'
import Password from 'primevue/password'
import Dropdown from 'primevue/dropdown'
import Menu from 'primevue/menu'
import Avatar from 'primevue/avatar'
import ProgressSpinner from 'primevue/progressspinner'
import Chip from 'primevue/chip'
import Divider from 'primevue/divider'
import Skeleton from 'primevue/skeleton'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import ConfirmPopup from 'primevue/confirmpopup'
import ConfirmationService from 'primevue/confirmationservice'
import StyleClass from 'primevue/styleclass'
import Badge from 'primevue/badge'
import Tooltip from 'primevue/tooltip'
import Dialog from 'primevue/dialog'
import Message from 'primevue/message'
import Image from 'primevue/image'
import Paginator from 'primevue/paginator'
import Ripple from 'primevue/ripple'
import TabMenu from 'primevue/tabmenu'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
import Sidebar from 'primevue/sidebar'

export const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(PrimeVue, { ripple: true })
app.use(ToastService)
app.use(VueApexCharts)
app.use(ConfirmationService)

app.directive('styleclass', StyleClass)
app.directive('tooltip', Tooltip)
app.directive('ripple', Ripple)

app.component(InputText.name, InputText)
app.component(Button.name, Button)
app.component(Checkbox.name, Checkbox)
app.component(Toast.name, Toast)
app.component(Password.name, Password)
app.component(Dropdown.name, Dropdown)
app.component(Menu.name, Menu)
app.component(Avatar.name, Avatar)
app.component(ProgressSpinner.name, ProgressSpinner)
app.component(Chip.name, Chip)
app.component(Divider.name, Divider)
app.component(Skeleton.name, Skeleton)
app.component(DataTable.name, DataTable)
app.component(Column.name, Column)
app.component(ConfirmPopup.name, ConfirmPopup)
app.component(Badge.name, Badge)
app.component(Dialog.name, Dialog)
app.component(Message.name, Message)
app.component(Image.name, Image)
app.component(Paginator.name, Paginator)
app.component(TabMenu.name, TabMenu)
app.component(TabView.name, TabView)
app.component(TabPanel.name, TabPanel)
app.component(Sidebar.name, Sidebar)

app.mount('#app')
