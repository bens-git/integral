<template>
  <v-container class="py-12">
    <v-row class="justify-center mb-8">
      <v-col cols="12" md="8" class="text-center">
        <ApplicationLogo width="72" height="72" class="mb-4" />
        <Head title="The Five Systems — INTEGRAL" />
        <h1 class="text-h4 mb-2">The Five Systems</h1>
        <p class="text-subtitle-1 mb-4">Integral coordinates production and governance through five interdependent systems: CDS, OAD, ITC, COS, and FRS — a cybernetic feedback loop without markets or central authority.</p>
        <v-btn text href="https://integralcollective.io/system/" target="_blank">View documentation</v-btn>
      </v-col>
    </v-row>

    <v-row class="system-cards" dense>
      <v-col cols="12" sm="6" md="4" lg="2" v-for="sys in systems" :key="sys.id">
        <v-card class="pa-4 sys-card" hover>
          <div class="sys-card-id">{{ sys.id }}</div>
          <div class="sys-card-name text-h6">{{ sys.name }}</div>
          <div class="sys-card-role mb-2">{{ sys.role }}</div>
          <div class="sys-card-modules" style="display:flex;align-items:center;justify-content:space-between;">
            <div class="modules" style="font-family: monospace; font-size:11px; color: rgba(0,0,0,0.6);">{{ sys.modules }}</div>
            <div>
              <v-btn small color="primary" v-if="sys.internal" @click="$inertia.visit(sys.route)">Open</v-btn>
              <v-btn small text v-else :href="sys.url" target="_blank">Learn</v-btn>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head } from '@inertiajs/vue3';
import { reactive } from 'vue';

const systems = reactive([
  { id: 'CDS', name: 'COLLABORATIVE DECISION SYSTEM', role: 'Democratic governance. Issues are raised, deliberated, decided by weighted consensus, and dispatched — with a complete tamper-evident record.', modules: '10 modules', internal: true, route: route('cds.dashboard') },
  { id: 'OAD', name: 'OPEN ACCESS DESIGN', role: 'Shared design commons. Production blueprints are authored collaboratively, ecologically assessed, certified, and freely available to all nodes.', modules: '10 modules', internal: false, url: 'https://integralcollective.io/system/#oad' },
  { id: 'ITC', name: 'INTEGRAL TIME CREDITS', role: 'Contribution accounting without money. Non-transferable, time-decaying credits that reflect real labor, materials, and ecological cost — not market value.', modules: '9 modules', internal: false, url: 'https://integralcollective.io/system/#itc' },
  { id: 'COS', name: 'COOPERATIVE ORGANIZATION', role: 'The engine room. Production plans are executed, labor coordinated, materials managed, quality assured — all generating the data the other systems need.', modules: '9 modules', internal: false, url: 'https://integralcollective.io/system/#cos' },
  { id: 'FRS', name: 'FEEDBACK & REVIEW SYSTEM', role: 'The cybernetic nervous system. Reads operational reality, diagnoses drift from principles, generates recommendations, and routes them back to CDS — closing the loop.', modules: '7 modules', internal: false, url: 'https://integralcollective.io/system/#frs' },
]);
</script>

<style scoped>
.sys-card { border-radius: 8px; min-height: 160px; display: flex; flex-direction: column; justify-content: space-between; }
.sys-card-id { font-family: monospace; font-size: 11px; color: rgba(0,0,0,0.6); letter-spacing: 1px; }
.sys-card-name { font-weight: 600; margin-top: 6px; }
.sys-card-role { font-size: 13px; color: rgba(0,0,0,0.7); }
.system-cards { gap: 16px; }
@media (max-width: 960px) {
  .system-cards { grid-template-columns: repeat(2, 1fr); }
}
</style>
