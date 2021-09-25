 <template>
  <div class="w-40">
    <Listbox v-model="selectedOption">
      <div class="relative mt-1">
        <ListboxButton
          class="
            relative
            w-full
            py-2
            pl-3
            pr-10
            text-left
            bg-white
            rounded-lg
            shadow
            cursor-default
            focus:outline-none
            focus-visible:ring-2
            focus-visible:ring-opacity-75
            focus-visible:ring-white
            focus-visible:ring-offset-orange-300
            focus-visible:ring-offset-2
            focus-visible:border-indigo-500
            sm:text-sm
          "
        >
          <span class="block truncate">{{ selectedOption }}</span>
          <span
            class="
              absolute
              inset-y-0
              right-0
              flex
              items-center
              pr-2
              pointer-events-none
            "
          >
            <SelectorIcon class="w-5 h-5 text-gray-400" aria-hidden="true" />
          </span>
        </ListboxButton>

        <transition
          leave-active-class="transition duration-100 ease-in"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <ListboxOptions
            class="
              absolute
              w-full
              py-1
              mt-1
              z-50
              overflow-auto
              text-base
              bg-white
              rounded-md
              shadow-lg
              max-h-60
              ring-1 ring-black ring-opacity-5
              focus:outline-none
              sm:text-sm
            "
          >
            <ListboxOption
              v-slot="{ active, selected }"
              v-for="option in options"
              :key="option"
              :value="option"
              as="template"
            >
              <li
                :class="[
                  active ? 'text-amber-900 bg-amber-100' : 'text-gray-900',
                  'cursor-default select-none relative py-2 pl-10 pr-4',
                ]"
              >
                <span
                  :class="[
                    selected ? 'font-medium' : 'font-normal',
                    'block truncate',
                  ]"
                  >{{ option }}</span
                >
                <span
                  v-if="selected"
                  class="
                    absolute
                    inset-y-0
                    left-0
                    flex
                    items-center
                    pl-3
                    text-amber-600
                  "
                >
                  <CheckIcon class="w-5 h-5" aria-hidden="true" />
                </span>
              </li>
            </ListboxOption>
            <li
              :class="['cursor-default select-none relative py-2 pl-10 pr-4']"
            >
              <input
                v-model="customValue"
                @keydown.space.prevent="overideSpace"
                @keydown.enter="customValueSelected"
                class="w-full outline-white"
                placeholder="custom"
              />

              <span
                v-if="isCustom"
                class="
                  absolute
                  inset-y-0
                  left-0
                  flex
                  items-center
                  pl-3
                  text-amber-600
                "
              >
                <CheckIcon class="w-5 h-5" aria-hidden="true" />
              </span>
            </li>
          </ListboxOptions>
        </transition>
      </div>
    </Listbox>
  </div>
</template>

<script>
import {
  Listbox,
  ListboxLabel,
  ListboxButton,
  ListboxOptions,
  ListboxOption,
} from "@headlessui/vue";
import { CheckIcon, SelectorIcon } from "@heroicons/vue/solid";
import toString from 'lodash/toString';

export default {
  components: {
    Listbox,
    ListboxLabel,
    ListboxButton,
    ListboxOptions,
    ListboxOption,
    CheckIcon,
    SelectorIcon,
  },
  props: {
    task: {
      type: Object,
      required: true,
    },
  },
  data() {
    const options = ["pending", "completed", "cancelled"];

    return {
      options,
      customValue: !options.includes(this.task.status) ? this.task.status : null,
      selectedOption: this.task.status,
    };
  },
  watch: {
    'task.status': function (v) {
        this.selectedOption = v
    },
    selectedOption(v) {
        this.updateStatus()
    },
    isCustom(v) {
        if (v === false) {
            this.customValue = null
        }
    }
  },
  computed: {
    isCustom() {
      return !this.options.includes(this.selectedOption);
    },
  },
  methods: {
    customValueSelected() {
      if (this.customValue) {
        this.selectedOption = this.customValue;
        if (!this.isCustom) {
          this.customValue = null;
        }
      }
    },
    overideSpace() {
      // Due to listbox expect a space as selection, we will
      // hijack the functionality for our text input to add space.
      this.customValue = this.customValue + " ";
    },
    async updateStatus() {
      try {
        await axios.put(route("tasks.status", this.task.id), {
          status: this.selectedOption,
        });
        this.$emit("updated", {...this.task, status: this.selectedOption });
      } catch (e) {
        this.selectedOption = this.task.status;
      }
    },
  },
};
</script>
