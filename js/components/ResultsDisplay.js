
export default {
    props: {
        results: {
            type: Number,
            required: true,
            default: 0,
        }
    },
    computed: {
      kilometers() {
          const copy = this.results;
          const km = Math.round(copy / 1000);
          const rkm = copy % 1000;
          return {
              km: km,
              rkm: rkm
          }
      }
    },
    template: `
        <div class="mt-6"  >
            
            <span class="uppercase font-medium text-lg">
                Distance between those coordinates is:
            </span>
            
            <div>
                <span v-if="kilometers.km > 0">
                    <span class="font-bold text-6xl">
                        {{ kilometers.km }}
                    </span>                
                    <span class="text-lg">km</span>
                </span>
                    &nbsp;
                <span class="font-bold text-6xl">
                    {{ kilometers.rkm }}
                </span> 
                <span class="text-lg">m</span>
            </div>
        </div>
    `
}