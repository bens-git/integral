<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.user);
const position = computed(() => page.props.position || {});
const currentNeeds = computed(() => page.props.currentNeeds || []);
const tasks = computed(() => page.props.tasks || []);
const resources = computed(() => page.props.resources || []);
const goals = computed(() => page.props.goals || []);

const getProgressPercentage = (current, total) => {
  return Math.round((current / total) * 100);
};
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <v-container fluid class="pa-0">
      <!-- Your Position Section -->
      <div class="section">
        <div class="section-label">YOUR POSITION</div>
        <div class="section-divider"></div>

        <div class="position-grid">
          <div class="position-item">
            <div class="position-value">{{ position.credits }}</div>
            <div class="position-label">Credits</div>
          </div>
          <div class="position-item">
            <div class="position-value">{{ position.energy_kwh?.toLocaleString() }}</div>
            <div class="position-label">kW·h net</div>
          </div>
          <div class="position-item">
            <div class="position-value">{{ position.consensus_pct }}%</div>
            <div class="position-label">Consensus</div>
          </div>
          <div class="position-item">
            <div class="position-value">{{ position.members_count }}</div>
            <div class="position-label">Members</div>
          </div>
        </div>
      </div>

      <!-- Current Needs Section -->
      <div class="section">
        <div class="section-header">
          <span class="section-icon">◆</span>
          <span>CURRENT NEEDS</span>
        </div>

        <div class="needs-list">
          <div v-for="need in currentNeeds" :key="need.id" class="need-item">
            <div class="need-header">
              <span class="need-icon">{{ need.icon }}</span>
              <span class="need-title">{{ need.title }}</span>
              <span class="need-progress">{{ need.progress }} / {{ need.total }}</span>
            </div>
            <div class="need-progress-bar">
              <div class="progress-fill" :style="{ width: getProgressPercentage(need.progress, need.total) + '%' }"></div>
            </div>
            <div class="need-footer">
              <span>{{ need.credits }} credits</span>
              <span>·</span>
              <span>{{ need.status }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Tasks and Resources Section -->
      <div class="section">
        <div class="tasks-resources-container">
          <!-- Tasks Column -->
          <div class="column">
            <div class="section-header">
              <span class="section-icon">◆</span>
              <span>YOUR TASKS</span>
            </div>

            <div class="tasks-list">
              <div v-for="task in tasks" :key="task.id" class="task-item">
                <div class="task-checkbox" :class="{ completed: task.completed }">
                  {{ task.completed ? '☑' : '☐' }}
                </div>
                <div class="task-content">
                  <div class="task-title">{{ task.title }}</div>
                  <div v-if="task.overdue" class="task-meta overdue">⚠ OVERDUE</div>
                  <div v-else-if="task.due" class="task-meta">Due in {{ task.due }}</div>
                  <div v-else class="task-meta">Completed</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Resources Column -->
          <div class="column">
            <div class="section-header">
              <span class="section-icon">◆</span>
              <span>RESOURCES</span>
            </div>

            <div class="resources-list">
              <div v-for="resource in resources" :key="resource.name" class="resource-item">
                <div class="resource-name">{{ resource.name }}</div>
                <div class="resource-value">{{ resource.value }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Goals Section -->
      <div class="section">
        <div class="section-header">
          <span class="section-icon">◆</span>
          <span>GOALS</span>
        </div>

        <div class="goals-list">
          <div v-for="goal in goals" :key="goal.title" class="goal-item">
            <div class="goal-title">{{ goal.title }}</div>
            <div class="goal-progress-container">
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: goal.progress + '%' }"></div>
              </div>
              <div class="progress-percentage">{{ goal.progress }}%</div>
            </div>
          </div>
        </div>
      </div>
    </v-container>
  </AuthenticatedLayout>
</template>

<style scoped>
:root {
  --primary: #1a1a1a;
  --secondary: #2d2d2d;
  --accent: #4a90e2;
  --accent-alt: #7b68ee;
  --success: #2ecc71;
  --warning: #f39c12;
  --danger: #e74c3c;
  --border: #e0e0e0;
  --text-primary: #1a1a1a;
  --text-secondary: #666;
  --bg-light: #f8f9fa;
}

.section {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 1px solid var(--border);
}

.section-label {
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--text-secondary);
  margin-bottom: 1rem;
  opacity: 0.8;
}

.section-divider {
  border-bottom: 2px solid var(--bg-light);
  margin-bottom: 1rem;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  margin-bottom: 1rem;
  color: var(--primary);
}

.section-icon {
  font-size: 1rem;
  color: var(--accent);
}

/* Position Section */
.position-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
}

.position-item {
  text-align: center;
  padding: 1rem;
  background: linear-gradient(135deg, rgba(74, 144, 226, 0.05) 0%, rgba(123, 104, 238, 0.05) 100%);
  border-radius: 8px;
  border: 1px solid rgba(74, 144, 226, 0.1);
  transition: all 0.3s ease;
}

.position-item:hover {
  box-shadow: 0 4px 12px rgba(74, 144, 226, 0.15);
  transform: translateY(-2px);
}

.position-value {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--accent);
  margin-bottom: 0.5rem;
}

.position-label {
  font-size: 0.7rem;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

@media (max-width: 700px) {
  .position-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }
  
  .position-item {
    padding: 0.75rem;
  }
  
  .position-value {
    font-size: 1.5rem;
  }
}

/* Current Needs Section */
.needs-list {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.need-item {
  padding: 1rem;
  background: var(--bg-light);
  border-radius: 8px;
  border: 1px solid var(--border);
  transition: all 0.3s ease;
}

.need-item:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transform: translateX(4px);
}

.need-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
  font-size: 0.95rem;
}

.need-icon {
  font-size: 1.25rem;
}

.need-title {
  font-weight: 600;
  flex: 1;
  color: var(--primary);
}

.need-progress {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--accent);
  background: rgba(74, 144, 226, 0.1);
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
}

.need-progress-bar {
  width: 100%;
  height: 6px;
  background: #e8eef7;
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 0.75rem;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--accent) 0%, var(--accent-alt) 100%);
  transition: width 0.5s ease;
  border-radius: 3px;
}

.need-footer {
  font-size: 0.8rem;
  color: var(--text-secondary);
  display: flex;
  gap: 0.5rem;
  font-weight: 500;
}

/* Tasks and Resources Section */
.tasks-resources-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

@media (max-width: 700px) {
  .tasks-resources-container {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}

.column {
}

.tasks-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.task-item {
  display: flex;
  gap: 0.75rem;
  padding: 0.85rem;
  background: var(--bg-light);
  border-radius: 8px;
  border-left: 3px solid var(--border);
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.task-item:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border-left-color: var(--accent);
}

.task-checkbox {
  font-size: 1rem;
  min-width: 1.25rem;
  height: 1.25rem;
  display: flex;
  align-items: center;
  color: var(--accent);
}

.task-checkbox.completed {
  color: var(--success);
}

.task-content {
  flex: 1;
}

.task-title {
  font-weight: 600;
  margin-bottom: 0.25rem;
  color: var(--primary);
}

.task-meta {
  font-size: 0.75rem;
  color: var(--text-secondary);
  font-weight: 500;
}

.task-meta.overdue {
  color: var(--danger);
  font-weight: 700;
}

/* Resources List */
.resources-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.resource-item {
  padding: 1rem;
  background: var(--bg-light);
  border-radius: 8px;
  border: 1px solid var(--border);
  border-left: 3px solid var(--accent);
  transition: all 0.3s ease;
}

.resource-item:hover {
  box-shadow: 0 4px 12px rgba(74, 144, 226, 0.15);
  transform: translateY(-2px);
}

.resource-name {
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 1px;
  color: var(--text-secondary);
  text-transform: uppercase;
  margin-bottom: 0.5rem;
}

.resource-value {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--accent);
}

/* Goals Section */
.goals-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.goal-item {
  padding: 0;
}

.goal-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--primary);
  margin-bottom: 0.75rem;
}

.goal-progress-container {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.progress-bar {
  flex: 1;
  height: 10px;
  background: #e8eef7;
  border-radius: 5px;
  overflow: hidden;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--accent) 0%, var(--accent-alt) 100%);
  border-radius: 5px;
  transition: width 0.5s ease;
}

.progress-percentage {
  font-size: 0.9rem;
  font-weight: 700;
  min-width: 3rem;
  text-align: right;
  color: var(--accent);
}
</style>
