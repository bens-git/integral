<template>
  <v-container class="pa-0">
    <!-- Hero Section -->
    <v-row class="fill-height min-vh-100 align-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
      <v-col cols="12" md="8" lg="6" class="mx-auto text-center pa-12">
        <v-card class="bg-transparent border-0">
          <v-card-text class="pb-0">
            <!-- Logo -->
            <div class="mb-8">
              <ApplicationLogo class="mx-auto" width="120" height="120" fill="white" />
            </div>

            <!-- Headline -->
            <h1 class="display-1 font-weight-bold text-white mb-4" style="line-height: 1.2;">
              Collaborative Decisions,<br class="d-md-none" /> Built on Consensus
            </h1>

            <!-- Subheadline -->
            <p class="headline text-white-70 mb-8" style="font-weight: 300; max-width: 600px; margin: 0 auto 2rem;">
              Integral is a collaborative decision system that helps teams frame issues,
              deliberate transparently, and reach durable consensus—without endless meetings.
            </p>

            <!-- CTA Buttons -->
            <div class="d-flex flex-wrap justify-center gap-4 mb-12">
              <v-btn
                size="x-large"
                color="white"
                variant="flat"
                class="text-primary font-weight-medium px-8 py-3"
                @click="$inertia.visit(route('login'))"
              >
                Get Started Free
                <v-icon end>mdi-arrow-right</v-icon>
              </v-btn>
              <v-btn
                size="x-large"
                variant="outlined"
                color="white"
                class="font-weight-medium px-8 py-3 border-2"
                @click="$inertia.visit(route('register'))"
              >
                Watch Demo
                <v-icon end>mdi-play-circle-outline</v-icon>
              </v-btn>
            </div>

            <!-- Trust indicators -->
            <div class="d-flex flex-wrap justify-center gap-6 text-white-60 text-body-2">
              <span class="d-flex align-center gap-2">
                <v-icon size="small">mdi-check-circle</v-icon>
                No credit card required
              </span>
              <span class="d-flex align-center gap-2">
                <v-icon size="small">mdi-check-circle</v-icon>
                14-day free trial
              </span>
              <span class="d-flex align-center gap-2">
                <v-icon size="small">mdi-check-circle</v-icon>
                Cancel anytime
              </span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Features Section -->
    <v-container class="py-20">
      <v-row class="justify-center mb-16">
        <v-col cols="12" md="10" lg="8" class="text-center">
          <h2 class="display-2 font-weight-bold mb-4">Why Teams Choose Integral</h2>
          <p class="headline text-grey-600" style="font-weight: 300;">
            Move from opinion-driven debates to structured, inclusive decisions that stick.
          </p>
        </v-col>
      </v-row>

      <v-row dense class="gx-12">
        <!-- Feature 1 -->
        <v-col cols="12" md="4">
          <v-card class="h-100 pa-8 elevation-1 hover-elevation-8 transition-all">
            <v-icon class="text-primary mb-4" size="x-large">mdi-forum-outline</v-icon>
            <h3 class="h5 font-weight-bold mb-3">Structured Issue Framing</h3>
            <p class="text-grey-600 body-1">
              Define decisions clearly with context, constraints, and success criteria before discussion begins. No more talking past each other.
            </p>
          </v-card>
        </v-col>

        <!-- Feature 2 -->
        <v-col cols="12" md="4">
          <v-card class="h-100 pa-8 elevation-1 hover-elevation-8 transition-all">
            <v-icon class="text-primary mb-4" size="x-large">mdi-account-group-outline</v-icon>
            <h3 class="h5 font-weight-bold mb-3">Inclusive Deliberation</h3>
            <p class="text-grey-600 body-1">
              Threaded, asynchronous discussions let every voice be heard. Participants contribute on their schedule, not in a rushed meeting.
            </p>
          </v-card>
        </v-col>

        <!-- Feature 3 -->
        <v-col cols="12" md="4">
          <v-card class="h-100 pa-8 elevation-1 hover-elevation-8 transition-all">
            <v-icon class="text-primary mb-4" size="x-large">mdi-hand-heart-outline</v-icon>
            <h3 class="h5 font-weight-bold mb-3">Consensus Building</h3>
            <p class="text-grey-600 body-1">
              Propose, discuss, and resolve objections systematically. Reach decisions everyone can commit to—even if it wasn't their first choice.
            </p>
          </v-card>
        </v-col>

        <!-- Feature 4 -->
        <v-col cols="12" md="4">
          <v-card class="h-100 pa-8 elevation-1 hover-elevation-8 transition-all">
            <v-icon class="text-primary mb-4" size="x-large">mdi-book-open-page-variant-outline</v-icon>
            <h3 class="h5 font-weight-bold mb-3">Decision Ledger</h3>
            <p class="text-grey-600 body-1">
              Every decision recorded with context, rationale, and participants. Institutional memory that survives team changes.
            </p>
          </v-card>
        </v-col>

        <!-- Feature 5 -->
        <v-col cols="12" md="4">
          <v-card class="h-100 pa-8 elevation-1 hover-elevation-8 transition-all">
            <v-icon class="text-primary mb-4" size="x-large">mdi-chart-timeline-variant</v-icon>
            <h3 class="h5 font-weight-bold mb-3">Progress Tracking</h3>
            <p class="text-grey-600 body-1">
              Visualize decision velocity, see bottlenecks, and measure how consensus quality improves over time.
            </p>
          </v-card>
        </v-col>

        <!-- Feature 6 -->
        <v-col cols="12" md="4">
          <v-card class="h-100 pa-8 elevation-1 hover-elevation-8 transition-all">
            <v-icon class="text-primary mb-4" size="x-large">mdi-shield-check-outline</v-icon>
            <h3 class="h5 font-weight-bold mb-3">Accountability Built-In</h3>
            <p class="text-grey-600 body-1">
              Clear ownership, deadlines, and follow-through tracking. Decisions don't just get made—they get executed.
            </p>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- How It Works Section -->
    <v-container class="py-20 grey lighten-4">
      <v-row class="justify-center mb-16">
        <v-col cols="12" md="10" lg="8" class="text-center">
          <h2 class="display-2 font-weight-bold mb-4">How It Works</h2>
          <p class="headline text-grey-600" style="font-weight: 300;">
            From fuzzy problem to clear decision in four steps.
          </p>
        </v-col>
      </v-row>

      <v-row dense class="gx-12">
        <v-col cols="12" md="3" v-for="(step, index) in steps" :key="step.title">
          <v-card class="h-100 pa-8 text-center elevation-0">
            <div class="mb-6">
              <v-chip
                size="x-large"
                color="primary"
                class="white--text font-weight-bold"
                style="width: 80px; height: 80px;"
              >
                {{ index + 1 }}
              </v-chip>
            </div>
            <h3 class="h5 font-weight-bold mb-3">{{ step.title }}</h3>
            <p class="text-grey-600 body-1">{{ step.description }}</p>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- CTA Section -->
    <v-container class="py-20">
      <v-row class="justify-center">
        <v-col cols="12" md="8" lg="6" class="text-center">
          <v-card
            class="pa-12"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
          >
            <v-card-text>
              <h2 class="display-2 font-weight-bold text-white mb-4">
                Ready to Decide Better?
              </h2>
              <p class="headline text-white-70 mb-8" style="font-weight: 300;">
                Join teams already using Integral to make faster, fairer, more durable decisions.
              </p>
              <div class="d-flex flex-wrap justify-center gap-4">
                <v-btn
                  size="x-large"
                  color="white"
                  variant="flat"
                  class="text-primary font-weight-medium px-8 py-3"
                  @click="$inertia.visit(route('register'))"
                >
                  Start Free Trial
                  <v-icon end>mdi-arrow-right</v-icon>
                </v-btn>
                <v-btn
                  size="x-large"
                  variant="outlined"
                  color="white"
                  class="font-weight-medium px-8 py-3 border-2"
                  @click="$inertia.visit(route('login'))"
                >
                  Sign In
                </v-btn>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- Footer -->
    <v-footer class="grey lighten-4 py-12" app>
      <v-container>
        <v-row class="align-center">
          <v-col cols="12" md="6" class="text-center text-md-start mb-4 mb-md-0">
            <ApplicationLogo class="mr-3" width="32" height="32" fill="primary" />
            <span class="font-weight-bold text-h6">Integral</span>
          </v-col>
          <v-col cols="12" md="6" class="text-center text-md-end">
            <span class="text-grey-600 body-2">
              © {{ new Date().getFullYear() }} Integral. Built for teams who decide together.
            </span>
          </v-col>
        </v-row>
      </v-container>
    </v-footer>
  </v-container>
</template>

<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { ref } from 'vue';

const steps = ref([
  {
    title: 'Frame the Issue',
    description: 'Define the decision, context, constraints, and what success looks like.'
  },
  {
    title: 'Deliberate',
    description: 'Structured async discussion with threads, proposals, and objections.'
  },
  {
    title: 'Build Consensus',
    description: 'Resolve objections systematically until everyone can commit.'
  },
  {
    title: 'Record & Execute',
    description: 'Decision logged with rationale. Owners, deadlines, and follow-through tracked.'
  }
]);
</script>

<style scoped>
.v-card {
  border-radius: 16px !important;
}

.v-btn {
  border-radius: 12px !important;
  text-transform: none !important;
}

.v-chip {
  border-radius: 50% !important;
}
</style>