<script setup lang="ts">
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuIndicator,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
  navigationMenuTriggerStyle,
  NavigationMenuViewport,
} from '@/components/ui/navigation-menu'
import { Link } from '@inertiajs/vue3';
</script>

<template>
<div class="">
    <div class="w-full px-10 py-4 bg-primary">
  <NavigationMenu class="">
    <NavigationMenuList class=" gap-4">
       <NavigationMenuItem>
        <NavigationMenuLink as-child :class="navigationMenuTriggerStyle()">
          <Link :href="route('home')">Home</Link>
        </NavigationMenuLink>
      </NavigationMenuItem>
       <NavigationMenuItem>
        <NavigationMenuLink as-child :class="navigationMenuTriggerStyle()">
          <Link :href="route('commission-report')">Commission Report</Link>
        </NavigationMenuLink>
      </NavigationMenuItem>
        <NavigationMenuItem>
        <NavigationMenuLink as-child :class="navigationMenuTriggerStyle()">
          <Link :href="route('top-distributors')">Top Distributors</Link>
        </NavigationMenuLink>
      </NavigationMenuItem>
    </NavigationMenuList>
  </NavigationMenu></div>
<main><slot/></main>
</div>
</template>
