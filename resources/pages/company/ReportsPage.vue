<template>
    <v-container class="py-6 px-14" fluid>
    <v-card class="pa-0" variant="flat">
      <v-card-title class="text-h5 pa-0">Reports</v-card-title>
      <v-card-subtitle class="pa-0">View your business analytics and reports.</v-card-subtitle>
      
      <v-divider class="my-6" />
      
      <v-row>
        <v-col v-for="i in 3" :key="i" cols="12" md="4">
          <v-card border flat class="rounded-xl pa-4">
            <div class="text-overline mb-1">Total Revenue</div>
            <div class="text-h4 font-weight-bold">$12,850</div>
            <div class="text-caption text-success mt-2">
              <v-icon icon="mdi-trending-up" size="small" /> +15% from last month
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-card>
  </v-container>
</template>
