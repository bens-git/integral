<template>
  <v-navigation-drawer
    v-model="localOpen"
    location="right"
    :permanent="isPermanent"
    :temporary="!isPermanent"
    width="360"
    class="chat-drawer"
  >
    <!-- Header -->
    <v-app-bar flat density="comfortable" class="chat-header">
      <v-app-bar-title>Chat</v-app-bar-title>
      <v-spacer />
      <v-btn icon variant="text" @click="close">
        <v-icon icon="mdi-close" />
      </v-btn>
    </v-app-bar>

    <v-divider />

    <!-- Online users -->
    <div class="online-bar">
      <div class="online-title">Online</div>
      <div class="online-list">
        <v-chip
          v-for="u in online"
          :key="u.id"
          size="small"
          class="ma-1"
          variant="tonal"
        >
          {{ u.name }}
        </v-chip>
      </div>
    </div>

    <v-divider />

    <!-- Messages -->
    <div ref="messagesContainer" class="messages">
      <div
        v-for="m in messages"
        :key="m.id"
        class="message-row"
        :class="{ mine: isMine(m) }"
      >
        <div class="bubble">
          <div class="meta">
            <span class="user">{{ m.user.name }}</span>
            <span class="time">{{ formatDate(m.created_at) }}</span>
          </div>

          <div class="body">
            {{ m.body }}
          </div>
        </div>
      </div>
    </div>

    <!-- Input -->
    <div class="composer">
      <v-text-field
        v-model="body"
        placeholder="Type a message..."
        density="comfortable"
        hide-details
        variant="outlined"
        class="composer-input"
        @keydown.enter.prevent="send"
      >
        <template #append-inner>
          <v-btn
            icon
            color="primary"
            variant="text"
            :disabled="sending || !body.trim()"
            @click="send"
          >
            <v-icon icon="mdi-send" />
          </v-btn>
        </template>
      </v-text-field>
    </div>
  </v-navigation-drawer>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from 'vue'
import axios from 'axios'
import { useDisplay } from 'vuetify'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({ modelValue: Boolean })
const emit = defineEmits(['update:modelValue', 'unread-count'])

const page = usePage()

const localOpen = ref(false)
const messages = ref([])
const online = ref([])
const body = ref('')
const sending = ref(false)

const messagesContainer = ref(null)

const { mdAndUp } = useDisplay()
const isPermanent = computed(() => mdAndUp.value)

const userId = computed(() => page.props.auth?.user?.id)

let messagesTimer = null
let onlineTimer = null
let pingTimer = null

// sync open state
watch(
  () => props.modelValue,
  v => (localOpen.value = !!v),
  { immediate: true }
)

watch(localOpen, v => {
  emit('update:modelValue', v)
  if (v || isPermanent.value) startPolling()
  else stopPolling()
})

watch(isPermanent, v => {
  if (v) startPolling()
})

function close() {
  if (!isPermanent.value) localOpen.value = false
}

function isMine(m) {
  return m.user?.id === userId.value
}

function formatDate(dt) {
  return new Date(dt).toLocaleTimeString([], {
    hour: '2-digit',
    minute: '2-digit'
  })
}

async function fetchMessages() {
  try {
    const { data } = await axios.get(route('chat.messages.index'))
    messages.value = data
    await nextTick()
    scrollBottom()
  } catch {}
}

async function fetchOnline() {
  try {
    const { data } = await axios.get(route('chat.online'))
    online.value = data
  } catch {}
}

async function send() {
  if (!body.value.trim() || sending.value) return

  sending.value = true
  try {
    await axios.post(route('chat.messages.store'), {
      body: body.value
    })

    body.value = ''
    await fetchMessages()
  } finally {
    sending.value = false
  }
}

function scrollBottom() {
  const el = messagesContainer.value
  if (!el) return
  el.scrollTop = el.scrollHeight
}

function startPolling() {
  if (messagesTimer) return

  fetchMessages()
  fetchOnline()

  messagesTimer = setInterval(fetchMessages, 2000)
  onlineTimer = setInterval(fetchOnline, 5000)
  pingTimer = setInterval(() => {
    axios.post(route('chat.ping')).catch(() => {})
  }, 10000)
}

function stopPolling() {
  clearInterval(messagesTimer)
  clearInterval(onlineTimer)
  clearInterval(pingTimer)

  messagesTimer = null
  onlineTimer = null
  pingTimer = null
}

onMounted(() => {
  if (localOpen.value || isPermanent.value) startPolling()
})

onUnmounted(() => stopPolling())
</script>

<style scoped>
.chat-drawer {
  display: flex;
  flex-direction: column;
}

/* online */
.online-bar {
  padding: 10px 12px;
}

.online-title {
  font-size: 12px;
  font-weight: 600;
  opacity: 0.7;
  margin-bottom: 6px;
}

.online-list {
  display: flex;
  flex-wrap: wrap;
}

/* messages area */
.messages {
  flex: 1;
  overflow-y: auto;
  padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  background: #fafafa;
}

/* message row */
.message-row {
  display: flex;
}

.message-row.mine {
  justify-content: flex-end;
}

/* bubble */
.bubble {
  max-width: 75%;
  padding: 10px 12px;
  border-radius: 14px;
  background: #ffffff;
  box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}

.message-row.mine .bubble {
  background: #e3f2fd;
}

/* meta */
.meta {
  display: flex;
  justify-content: space-between;
  font-size: 11px;
  opacity: 0.6;
  margin-bottom: 4px;
}

.user {
  font-weight: 600;
}

/* message body */
.body {
  font-size: 14px;
  white-space: pre-wrap;
  word-break: break-word;
}

/* composer */
.composer {
  padding: 10px;
  border-top: 1px solid rgba(0,0,0,0.08);
  background: white;
}

.composer-input {
  font-size: 14px;
}
</style>