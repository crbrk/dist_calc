
import ResultsDisplay from "./ResultsDisplay.js";

export default {
    data() {
        return {
            latitude_from: '',
            longitude_from: '',
            latitude_to: '',
            longitude_to: '',
            results: null,
            error: '',
        }
    },
    components: {
        ResultsDisplay
    },
    methods: {
        resetForm() {
            this.latitude_from = '';
            this.longitude_from ='';
            this.latitude_to = '';
            this.longitude_to = '';
            this.results = null;
            this.error = ''
        },
        handleSubmit() {
            this.error = '';
            const url = 'src/processForm.php';
            const options = {
                method: 'POST',
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    latitude_from:  this.latitude_from,
                    longitude_from: this.longitude_from,
                    latitude_to:    this.latitude_to,
                    longitude_to:   this.longitude_to
                })
            };

            fetch(url, options)
                .then(async response => {
                    if (response.ok) {
                        return response.json();
                    }
                    else {
                        let resp = await response.json();
                        throw new Error(resp.message)
                    }

                } )
                .then(json =>{
                    this.results = json.message;
                })
                .catch(error => {
                    this.error = error.message;
            });

        }
    },
    template: `
        <div class="mt-12 bg-white border border-black-700">
            
                <div class="grid grid-cols-10 border bg-gray-600 text-white text-sm font-mono">
                    <div class="col-span-2"></div>
                    <div class="col-span-4">allowed latitude values: <br/>  -90.0 to 90.0 </div>
                    <div class="col-span-4">allowed longitude values: <br/>  -180.0 to 180.0 </div>
                </div>
                <form v-on:submit.prevent="handleSubmit">
                    
                        <div v-if="error" class="alert alert-danger w-full bg-red-400 text-xl font-bold uppercase" >
                            {{ error }}
                        </div>
                    
                    
                        <div class="grid grid-cols-10 mt-6 ">
                            <div class="col-span-2 "></div>
                            <div class="col-span-4 text-gray-700 uppercase font-bold">point #1</div>
                            <div class="col-span-4 text-gray-700 uppercase font-bold">point #2</div>  
                       </div>
                        
                        
                        
                        <div class="grid grid-cols-10">
                            
                            <div class="py-2 mt-6 col-span-2 flex content-start flex">
                                <span class="text-gray-700 uppercase font-bold my-2 mx-auto">latitude</span>
                            </div>
                        
                            <div class="py-2 mt-6 col-span-4 ">
                                <input type="text" v-model="latitude_from" 
                                class="border-b border-b-2 border-teal-500 w-4/5 text-gray-700 px-3 text-2xl font-bold focus:outline-none" id="latitude-field-from"
                                aria-describedby="latitude-field-from" placeholder="input latitude">
                            </div>
                            
                            <div class="py-2 mt-6 col-span-4">
                                <input type="text" v-model="latitude_to" 
                                class="border-b border-b-2 border-teal-500 w-4/5 text-gray-700 px-3 text-2xl font-bold  focus:outline-none" id="latitude-field-to"
                                aria-describedby="latitude-field-to" placeholder="input latitude">
                            </div>
                        </div>
                            
                        <div class="grid grid-cols-10">
                        
                            <div class="py-2 mt-6 col-span-2 flex content-start flex">
                                <span class="text-gray-700 uppercase font-bold my-2 mx-auto">longitude</span>
                            </div>
                        
                            <div class="py-2 mt-6 col-span-4">
                                <input type="text" v-model="longitude_from" 
                                class="border-b border-b-2 border-teal-500 w-4/5 text-gray-700 px-3 text-2xl font-bold  focus:outline-none" id="longitude-field-from"
                                aria-describedby="longitude-field-from" placeholder="input longitude">
                            </div>
                        
                        
                      
                            <div class="py-2 mt-6 col-span-4">
                                <input type="text" v-model="longitude_to" 
                                class="border-b border-b-2 border-teal-500 w-4/5 text-gray-700 px-3 text-2xl font-bold focus:outline-none" id="longitude-field-to"
                                aria-describedby="longitude-field-to" placeholder="input longitude">
                            </div>
                        </div>
                        
                        
                        
                        <button class="bg-teal-500 hover:bg-teal-700 border-teal-500 hover:border-teal-700 text-sm border-4 text-white py-1 px-2 mt-5 w-full rounded uppercase"
                                type="submit">
                                calculate distance
                        </button>
                        
                        <button v-on:click="resetForm" 
                                type="button"
                                class="hover:bg-red-500 ml-auto flex mt-2 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent uppercase rounded"
                                >
                            reset
                        </button>
                     
                </form>

            <ResultsDisplay v-bind:results="results" v-if="results !== null"></ResultsDisplay>
        </div>
    `,
};
