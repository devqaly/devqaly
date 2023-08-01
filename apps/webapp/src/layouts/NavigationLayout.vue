<template>
  <div>
    <div class="min-h-screen flex relative lg:static surface-ground">
      <div
        id="app-sidebar-1"
        class="bg-bluegray-800 h-screen hidden lg:block flex-shrink-0 absolute lg:static left-0 top-0 z-1 select-none"
        style="width: 280px"
      >
        <div class="flex flex-column h-full">
          <div class="flex align-items-center bg-bluegray-900 flex-shrink-0 justify-content-center">
            <Image
              class="my-2"
              src="/logo.svg"
              alt="Logo"
              height="45"
            />
          </div>

          <div
            class="mx-3 p-3 mt-2 border-round-md flex align-items-center text-white bg-bluegray-900 cursor-pointer"
          >
            <div
              class="flex flex-column flex-grow-1"
              aria-controls="companies-menu"
              @click="onToggleCompaniesMenu"
            >
              <div class="font-semibold text-2xl">
                {{ appStore.activeCompany ? appStore.activeCompany.name : '...' }}
              </div>
              <div class="font-medium">
                {{ appStore.loggedUser ? appStore.loggedUser.fullName : '...' }}
              </div>
            </div>

            <Menu
              ref="companiesMenu"
              id="companies-menu"
              :model="companiesItems"
              :popup="true"
            />

            <div class="flex-grow-0"><i class="pi pi-chevron-down"></i></div>
          </div>

          <div class="overflow-y-auto">
            <ul class="list-none px-3 pt-0">
              <!--              <li>-->
              <!--                <router-link-->
              <!--                  active-class="bg-bluegray-900 text-bluegray-50"-->
              <!--                  :to="{ name: 'dashboard' }"-->
              <!--                  v-ripple-->
              <!--                  class="flex align-items-center cursor-pointer p-3 mt-1 hover:bg-bluegray-900 border-round text-bluegray-100 hover:text-bluegray-50 transition-duration-150 transition-colors p-ripple no-underline"-->
              <!--                >-->
              <!--                  <i class="pi pi-home mr-2"></i>-->
              <!--                  <span class="font-medium">Dashboard</span>-->
              <!--                </router-link>-->
              <!--              </li>-->
              <li>
                <router-link
                  active-class="bg-bluegray-900 text-bluegray-50"
                  :to="{
                    name: 'listCompanyMembers',
                    params: { companyId: appStore.activeCompany!.id }
                  }"
                  v-ripple
                  class="flex align-items-center cursor-pointer p-3 mt-1 hover:bg-bluegray-900 border-round text-bluegray-100 hover:text-bluegray-50 transition-duration-150 transition-colors p-ripple no-underline"
                >
                  <i class="pi pi-users mr-2"></i>
                  <span class="font-medium">Members</span>
                </router-link>
              </li>
              <li>
                <router-link
                  active-class="bg-bluegray-900 text-bluegray-50"
                  :to="{ name: 'listProjects' }"
                  v-ripple
                  class="flex align-items-center cursor-pointer p-3 mt-1 hover:bg-bluegray-900 border-round text-bluegray-100 hover:text-bluegray-50 transition-duration-150 transition-colors p-ripple no-underline"
                >
                  <i class="pi pi-server mr-2"></i>
                  <span class="font-medium">Projects</span>
                </router-link>
              </li>
              <li>
                <router-link
                  active-class="bg-bluegray-900 text-bluegray-50"
                  :to="{ name: 'projectCreate' }"
                  v-ripple
                  class="flex align-items-center cursor-pointer p-3 mt-1 hover:bg-bluegray-900 border-round text-bluegray-100 hover:text-bluegray-50 transition-duration-150 transition-colors p-ripple no-underline"
                >
                  <i class="pi pi-plus mr-2"></i>
                  <span class="font-medium">Create Project</span>
                </router-link>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="min-h-screen flex flex-column relative flex-auto">
        <div
          class="flex justify-content-between align-items-center px-5 surface-0 border-bottom-1 surface-border relative lg:static shadow-2"
          style="height: 60px"
        >
          <div class="flex">
            <a
              v-ripple
              class="cursor-pointer block lg:hidden text-700 mr-3 mt-1 p-ripple"
              v-styleclass="{
                selector: '#app-sidebar-1',
                enterClass: 'hidden',
                enterActiveClass: 'fadeinleft',
                leaveToClass: 'hidden',
                leaveActiveClass: 'fadeoutleft',
                hideOnOutsideClick: true
              }"
            >
              <i class="pi pi-bars text-4xl"></i>
            </a>
          </div>

          <div>
            <Button
              type="button"
              icon="pi pi-user"
              text
              rounded
              @click="onToggleMenu"
              aria-haspopup="true"
              aria-controls="overlay_menu"
            />
            <Menu
              ref="menu"
              id="overlay_menu"
              :model="[]"
              :popup="true"
            >
              <template #end>
                <router-link
                  :to="{ name: 'authLogout' }"
                  class="w-full flex align-items-center p-2 pl-4 text-color hover:surface-200 border-noround no-underline"
                >
                  <i class="pi pi-sign-out" />
                  <span class="ml-2">Log Out</span>
                </router-link>
              </template>
            </Menu>
          </div>
        </div>
        <div
          class="flex flex-column flex-auto"
          style="height: 80vh; overflow-y: scroll"
        >
          <router-view />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import Menu from 'primevue/menu'
import { useAppStore } from '@/stores/app'
import { usePrimeVue } from 'primevue/config'
import type { CompanyCodec } from '@/services/api/resources/company/codec'
import { useRouter } from 'vue-router'

// @see: https://github.com/primefaces/primevue/issues/2454
const $primevue = usePrimeVue()

// @see: https://github.com/primefaces/primevue/issues/2454
defineExpose({
  $primevue
})

const menu = ref<InstanceType<typeof Menu>>()

const router = useRouter()

const companiesMenu = ref<InstanceType<typeof Menu>>()

const companiesItems = computed(() =>
  appStore.loggedUserCompanies.data.map((company) => ({
    label: company.name,
    command: () => setActiveCompany(company)
  }))
)

const appStore = useAppStore()

function onToggleMenu(event: Event) {
  menu.value?.toggle(event)
}

function onToggleCompaniesMenu(event: Event) {
  companiesMenu.value?.toggle(event)
}

function setActiveCompany(company: CompanyCodec) {
  appStore.activeCompany = company

  router.push({ name: 'listProjects' })
}
</script>
