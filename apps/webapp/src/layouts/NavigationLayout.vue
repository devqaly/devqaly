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
            @click="onToggleCompaniesMenu"
          >
            <div
              class="flex flex-column flex-grow-1"
              aria-controls="companies-menu"
            >
              <div
                class="font-semibold text-2xl"
                data-cy="navigation-layout__active-company"
              >
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
            <a
              target="_blank"
              href="https://github.com/devqaly/devqaly/issues/new?assignees=&labels=type%3A+bug&projects=&template=bug_report.yml&title=%F0%9F%90%9B+Bug+Report%3A+"
            >
              <Button
                v-tooltip.bottom="'Report Bug'"
                type="button"
                text
                rounded
              >
                <template #icon>
                  <svg
                    fill="#3c82f6"
                    xmlns="http://www.w3.org/2000/svg"
                    height="1em"
                    viewBox="0 0 512 512"
                  >
                    <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                    <path
                      d="M256 0c53 0 96 43 96 96v3.6c0 15.7-12.7 28.4-28.4 28.4H188.4c-15.7 0-28.4-12.7-28.4-28.4V96c0-53 43-96 96-96zM41.4 105.4c12.5-12.5 32.8-12.5 45.3 0l64 64c.7 .7 1.3 1.4 1.9 2.1c14.2-7.3 30.4-11.4 47.5-11.4H312c17.1 0 33.2 4.1 47.5 11.4c.6-.7 1.2-1.4 1.9-2.1l64-64c12.5-12.5 32.8-12.5 45.3 0s12.5 32.8 0 45.3l-64 64c-.7 .7-1.4 1.3-2.1 1.9c6.2 12 10.1 25.3 11.1 39.5H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H416c0 24.6-5.5 47.8-15.4 68.6c2.2 1.3 4.2 2.9 6 4.8l64 64c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0l-63.1-63.1c-24.5 21.8-55.8 36.2-90.3 39.6V240c0-8.8-7.2-16-16-16s-16 7.2-16 16V479.2c-34.5-3.4-65.8-17.8-90.3-39.6L86.6 502.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l64-64c1.9-1.9 3.9-3.4 6-4.8C101.5 367.8 96 344.6 96 320H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H96.3c1.1-14.1 5-27.5 11.1-39.5c-.7-.6-1.4-1.2-2.1-1.9l-64-64c-12.5-12.5-12.5-32.8 0-45.3z"
                    />
                  </svg>
                </template>
              </Button>
            </a>

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

const companiesItems = computed(() => [
  ...appStore.loggedUserCompanies.data.map((company) => ({
    label: company.name,
    icon: 'pi pi-building',
    command: () => setActiveCompany(company)
  })),
  { separator: true },
  {
    icon: 'pi pi-plus',
    label: 'Create Company',
    command: () => router.push({ name: 'createCompany' })
  }
])

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
