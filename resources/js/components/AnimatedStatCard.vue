<template>
  <div class="animated-stat-card p-4 rounded-4 text-center border shadow-sm position-relative overflow-hidden" @click="nextStat" style="cursor: pointer;">
    <!-- Icon & Header Badge -->
    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
      <span class="stat-icon-badge shadow-sm" :key="currentIndex">
        <i :class="currentStat.icon"></i>
      </span>
      <Transition name="fade-slide" mode="out-in">
        <span :key="currentIndex" class="stat-title font-weight-bold">
          {{ currentStat.label }}
        </span>
      </Transition>
    </div>

    <!-- Number Flow Value -->
    <div class="stat-number-wrapper my-2">
      <NumberFlow
        :value="currentStat.value"
        :format="{ useGrouping: true }"
        class="stat-number-flow"
      />
    </div>

    <!-- Step Navigation Dots -->
    <div class="d-flex justify-content-center align-items-center gap-2 mt-2">
      <span
        v-for="(item, index) in stats"
        :key="index"
        class="nav-dot"
        :class="{ active: index === currentIndex }"
        @click.stop="goToStat(index)"
      ></span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import NumberFlow from '@number-flow/vue';

const props = defineProps({
  weeklyMeetings: { type: [Number, String], default: 0 },
  groupsCount: { type: [Number, String], default: 0 },
  weeklyMeetingsLabel: { type: String, default: 'اجتماعات أسبوعية' },
  groupsCountLabel: { type: String, default: 'مجموعات' }
});

const stats = computed(() => [
  {
    label: props.weeklyMeetingsLabel,
    value: Number(props.weeklyMeetings) || 0,
    icon: 'bi bi-calendar-week-fill'
  },
  {
    label: props.groupsCountLabel,
    value: Number(props.groupsCount) || 0,
    icon: 'bi bi-people-fill'
  }
]);

const currentIndex = ref(0);
let timer = null;

const currentStat = computed(() => stats.value[currentIndex.value]);

const nextStat = () => {
  currentIndex.value = (currentIndex.value + 1) % stats.value.length;
  resetTimer();
};

const goToStat = (index) => {
  currentIndex.value = index;
  resetTimer();
};

const resetTimer = () => {
  if (timer) clearInterval(timer);
  timer = setInterval(() => {
    currentIndex.value = (currentIndex.value + 1) % stats.value.length;
  }, 4000);
};

onMounted(() => {
  resetTimer();
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});
</script>

<style scoped>
.animated-stat-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(50, 85, 127, 0.15) !important;
  border-radius: 20px !important;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  min-height: 180px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: relative !important;
  overflow: hidden !important;
}

.animated-stat-card::before {
  content: "" !important;
  position: absolute !important;
  left: -20px !important;
  bottom: -20px !important;
  width: 130px !important;
  height: 130px !important;
  background-image: url('/assets/images/na-logo-blue.png') !important;
  background-size: contain !important;
  background-repeat: no-repeat !important;
  opacity: 0.05 !important;
  pointer-events: none !important;
  z-index: 0 !important;
}

.animated-stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 28px rgba(50, 85, 127, 0.12) !important;
}

.stat-icon-badge {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: linear-gradient(135deg, #32557f 0%, #00698f 100%);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}

.stat-title {
  color: #32557f;
  font-size: 1.15rem;
  font-weight: 700;
}

.stat-number-flow {
  font-size: 2.8rem;
  font-weight: 800;
  color: #32557f;
  line-height: 1;
  font-family: monospace, system-ui, -apple-system;
}

.nav-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: rgba(50, 85, 127, 0.25);
  cursor: pointer;
  transition: all 0.3s ease;
}

.nav-dot.active {
  width: 24px;
  border-radius: 12px;
  background: #32557f;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(6px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>
