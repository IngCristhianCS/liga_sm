<x-app-layout>
    
<!-- Main Content -->
<div id="app">
    <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
</div>
</x-app-layout>
