import {Store} from 'vuex';

declare module 'vue/types/vue' {
  interface VueConstructor {
    resource: any;
  }
  interface Vue {
    resource: any;
  }
}

declare module '@vue/runtime-core' {
  interface CombinedVueInstance {
    trans: (text: string) => string;
  }

  // provide typings for `this.$store`
  interface ComponentCustomProperties {
    $store: Store<any>
  }
}
