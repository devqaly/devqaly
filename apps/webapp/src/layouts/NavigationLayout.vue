<template>
  <div>
    <div class="min-h-screen flex relative lg:static bg-slate-100">
      <div
        id="app-sidebar-1"
        class="bg-slate-50 md:bg-transparent h-screen hidden lg:block shrink-0 absolute lg:static left-0 top-0 z-[1] select-none shadow-2xl md:shadow-none"
        style="width: 280px"
      >
        <div class="flex flex-col h-full">
          <div class="flex items-center shrink-0 content-center flex-col">
            <Image
              class="mt-4 max-w-[10rem]"
              src="/logo--dark.svg"
              alt="Logo"
              height="45"
            />

            <div class="bg-blue-500 px-2 py-1 text-white rounded-full font-bold text-xs mb-4 mt-2">
              beta
            </div>
          </div>

          <hr
            class="h-px mt-0 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent mx-4"
          />

          <div
            class="mx-3 p-3 mt-4 rounded-md flex items-center text-white cursor-pointer bg-gradient-to-tr from-blue-500 to-blue-300"
            @click="onToggleCompaniesMenu"
          >
            <div
              class="flex flex-col grow"
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

            <div class="grow-0"><i class="pi pi-chevron-down"></i></div>
          </div>

          <div class="overflow-y-auto">
            <ul class="list-none px-3 py-5">
              <!--              <li>-->
              <!--                <router-link-->
              <!--                  active-class="bg-bluegray-900 text-bluegray-50"-->
              <!--                  :to="{ name: 'dashboard' }"-->
              <!--                  v-ripple-->
              <!--                  class="flex items-center cursor-pointer p-3 mt-1 hover:bg-bluegray-900 rounded-md text-bluegray-100 hover:text-bluegray-50 transition-duration-150 transition-colors p-ripple no-underline"-->
              <!--                >-->
              <!--                  <i class="pi pi-home mr-2"></i>-->
              <!--                  <span class="font-medium">Dashboard</span>-->
              <!--                </router-link>-->
              <!--              </li>-->
              <li>
                <router-link
                  active-class="bg-white shadow-lg"
                  :to="{
                    name: 'listCompanyMembers',
                    params: { companyId: appStore.activeCompany!.id }
                  }"
                  class="flex items-center cursor-pointer p-4 rounded-md text-slate-500 transition-all mt-2"
                >
                  <i class="pi pi-users mr-2 bg-white p-2.5 rounded-md text-slate-800"></i>
                  <span class="font-medium">Members</span>
                </router-link>
              </li>
              <li>
                <router-link
                  active-class="bg-white shadow-lg"
                  :to="{ name: 'listProjects', params: { companyId: appStore.activeCompany!.id } }"
                  class="flex items-center cursor-pointer p-4 rounded-md text-slate-500 transition-all"
                >
                  <i class="pi pi-server mr-2 bg-white p-2.5 rounded-md text-slate-800"></i>
                  <span class="font-medium">Projects</span>
                </router-link>
              </li>
              <li>
                <router-link
                  active-class="bg-white shadow-lg"
                  :to="{ name: 'projectCreate', params: { companyId: appStore.activeCompany!.id } }"
                  class="flex items-center cursor-pointer p-4 rounded-md text-slate-500 transition-all"
                >
                  <i class="pi pi-plus mr-2 bg-white p-2.5 rounded-md text-slate-800"></i>
                  <span class="font-medium">Create Project</span>
                </router-link>
              </li>
            </ul>
          </div>

          <div class="mt-auto">
            <div class="relative break-words rounded-2xl mx-3 block text-white overflow-hidden">
              <div
                class="absolute bg-center bg-cover w-full h-full rounded-2xl -z-10 brightness-75"
                style="background-image: url('/images/backgrounds/topography.png')"
              />

              <div class="p-3">
                <div class="bg-white p-1 w-fit rounded-lg flex items-center justify-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    class="w-8 h-8 stroke-slate-500"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"
                    />
                  </svg>
                </div>

                <div class="mt-3">
                  <div class="font-semibold text-2xl">Need Help?</div>
                  <div class="font-semibold text-xs mb-4">We are here to help you</div>

                  <a
                    href="https://github.com/devqaly/devqaly/issues/new?assignees=&labels=type%3A+bug&projects=&template=bug_report.yml&title=%F0%9F%90%9B+Bug+Report%3A+"
                    target="_blank"
                    class="inline-block w-full py-2 mb-0 font-bold text-center text-black uppercase transition-all ease-in bg-white border-0 border-white rounded-lg shadow-soft-md bg-150 leading-pro text-xs hover:shadow-soft-2xl"
                  >
                    Report a bug
                  </a>

                  <a
                    href="https://docs.devqaly.com"
                    target="_blank"
                    class="inline-block w-full py-2 mb-0 font-bold text-center text-black uppercase transition-all ease-in bg-white border-0 border-white rounded-lg shadow-soft-md bg-150 leading-pro text-xs hover:shadow-soft-2xl mt-2"
                  >
                    Documentation
                  </a>
                </div>
              </div>
            </div>

            <router-link
              :to="{ name: 'authLogout' }"
              class="mx-4 bg-white flex my-2 justify-center rounded-md py-2 font-bold text-xs shadow-sm"
            >
              Logout
            </router-link>
          </div>
        </div>
      </div>
      <div class="min-h-screen flex flex-col relative flex-auto">
        <div
          class="flex content-between items-center px-5 bg-transparent relative lg:static md:hidden"
          style="height: 60px"
        >
          <div class="flex">
            <a
              v-ripple
              class="cursor-pointer block lg:hidden text-slate-700 mr-3 mt-1 p-ripple"
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
        </div>
        <div
          class="flex flex-col flex-auto"
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
import type { CompanyCodec } from '@/services/api/resources/company/codec'
import { useRouter } from 'vue-router'

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

function onToggleCompaniesMenu(event: Event) {
  companiesMenu.value?.toggle(event)
}

function setActiveCompany(company: CompanyCodec) {
  appStore.activeCompany = company

  router.push({ name: 'listProjects', params: { companyId: company.id } })
}
</script>
