<template>
  <div class="bg-slate-50 px-4 py-8 md:px-6 lg:px-8 grid items-center justify-center h-screen">
    <div class="flex flex-col lg:flex-row items-center justify-center gap-7">
      <div class="text-center lg:text-right">
        <div class="mt-6 mb-3 font-bold text-6xl text-900">Are you lost?</div>
        <p class="text-700 text-3xl mt-0 mb-6">Sorry, we couldn't find the page.</p>
        <router-link :to="{ name: 'listProjects' }">
          <Button
            type="button"
            outlined
            icon="pi pi-home"
            label="Go To Projects"
          ></Button>
        </router-link>
      </div>
      <div>
        <Image
          src="/images/illustrations/404-rocket.png"
          alt="Image"
          class="w-full md:w-28rem"
        />
      </div>
    </div>
  </div>
</template>
