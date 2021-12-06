<template>
    <div class="p-4 w-full">
        <Notification v-on:close="close" :message="message" :success="success" :error="error"></Notification>
        <div v-if="!connectionError">
            <div v-if="isStockLoading">
                <svg v-if="reportLoading" class="animate-spin h-4 w-4 mt-1 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="green" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div v-else>
                <div v-if="stock.length == 0" class="flex w-full p-4 mb-4 border bg-white rounded-lg">
                    * Please import your csv file to analyse buy & sell details
                    <a href="sample-template.csv" class="-mt-1 flex w-56 ml-2 text-sm px-2 py-2 rounded-md shadow-sm text-black bg-blue-300 hover:bg-blue-400" style="text-decoration:none" download>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Sample Template
                    </a>
                </div>
                <div v-else class="p-3 flex border border-gray-200 mb-4 rounded-lg bg-white">
                    <div class="w-1/4 p-2">
                    <label class="m-1 mb-2">Company</label>
                    <vue-select placeholder="Select stock" class="bg-gray-200" :options="stock" v-model="company"></vue-select>
                    </div>
                    <div class="w-1/4 p-2">
                    <label class="m-1 mb-2">Date :: From</label>
                    <datepicker :bootstrap-styling="true" :format="customFormatter" v-model="dateFrom" class="rounded bg-white"></datepicker>
                    </div>
                    <div class="w-1/4 p-2">
                    <label class="m-1 mb-2">Date :: To</label>
                    <datepicker :bootstrap-styling="true" :format="customFormatter" v-model="dateTo" class="rounded bg-white"></datepicker>
                    </div>
                    <div class="w-1/4 p-2 pt-3">
                        <div class="flex mt-4">
                            <button v-if="company" @click="reset()" class="flex py-2 mr-5 px-3 shadow-sm rounded border-2 border-gray-600 hover:bg-gray-900 hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                </svg>
                                Reset
                            </button>
                            <button @click="report" class="flex float-right py-2 px-4 shadow-sm rounded border-2 border-gray-600 hover:bg-gray-900 hover:text-white">
                                <svg v-if="reportLoading" class="animate-spin h-4 w-4 mt-1 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="green" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 -mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l2.879-2.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            <span class="px-3">Search</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="stockReport.length != 0" class="flex w-full mb-4">
                <div class="pr-2 w-1/2">
                    <div class="bg-white border rounded-lg">
                        <div class="p-3 border-b mb-1 rounded-t-lg border-green-200 bg-green-50 text-green-500 font-extrabold text-center uppercase">Buy</div>
                        <div class="p-3">
                            <h6>Buy Price :: {{ stockReport['purchase_price'] ? '₹'+stockReport['purchase_price'] : 'N/A' }}</h6>
                            <h6 class="mt-4">Buy Date  :: {{ stockReport['purchase_on'] ? stockReport['purchase_on'] : 'N/A' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="pl-2 w-1/2">
                    <div class="bg-white border rounded-lg">
                        <div class="p-3 border-b mb-1 rounded-t-lg border-red-200 bg-red-100 text-red-500 font-extrabold text-center uppercase">Sell</div>
                        <div class="p-3">
                            <h6>Sell Price :: {{ '₹'+stockReport['sell_price'] }}</h6>
                            <h6 class="mt-4">Sell Date  :: {{ stockReport['sell_on'] }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="importError.length != 0" class="table rounded-lg border mt-4 bg-white">
                <div class="flex w-full p-3 text-red-600 bg-red-50">
                    <label class="w-full mt-1"># Import Error : We couldn't import these records</label>
                    <button @click="closeImportError()" class="border bg-gray-300 rounded-lg py-1 px-4 text-black hover:shadow-md">close</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th class="p-3 w-1/4">Stock</th>
                            <th class="p-3 w-1/4">Price</th>
                            <th class="p-3 w-1/4">Date</th>
                            <th class="p-3 w-1/4">Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="error in importError" v-bind:key="error.index">
                            <td class="p-2 w-1/4">
                            <div class="w-96 truncate">{{ error[2] }}</div>
                            </td>
                            <td class="p-2 w-1/4">{{ error[3] }}</td>
                            <td class="p-2 w-1/4">{{ error[1] }}</td>
                            <td class="p-2 w-1/4">{{ error['msg'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="rounded-lg bg-white p-4 border border-gray-200 w-full"> 
                <div v-if="loading" class="px-4 py-10 bg-gray-900 text-white w-full flex justify-center">
                    <div>
                        <svg class="animate-spin mb-3 h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Please wait uploading...
                    </div>
                </div>
                <div v-else class="border-dashed bg-gray-50 text-gray-400 h-80 relative text-sm border-2 border-gray-200 hover:border-purple-500 hover:bg-purple-50 hover:text-purple-900 rounded p-4">
                    <input type="file" ref="csv" v-on:change="uploadFile" id="csv" class="opacity-0 w-full h-full absolute top-0 z-10" style="cursor:pointer">
                    <div class="absolute w-full top-0 flex justify-center bottom-0 h-10 m-auto z-0">
                        <div class="-mt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z" />
                                <path d="M9 13h2v5a1 1 0 11-2 0v-5z" />
                            </svg>
                            <h6 class="mt-3 text-center font-light">
                                Click here to upload your file
                                <br>or<br>
                                Drag & Drop your CSV file here
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

 <script>
    import axios from 'axios'
    import Vue from 'vue'
    import Notification from './Notification.vue'
    import VueSelect from 'vue-select'
    import 'vue-select/dist/vue-select.css'
    import Datepicker from 'vuejs-datepicker';
    import moment from 'moment';

    export default {
        components: {
            Notification,
            VueSelect,
            Datepicker,
        },
        mounted(){
            this.getStocks()
        },
        data() {
            return {
                csv           : '',
                loading       : 0,
                error         : 0,
                success       : 0,
                importError   : [],
                message       : '',
                stock         : [],
                company       : '',
                reportLoading : 0,
                isStockLoading: 1,
                dateFrom      : moment().toDate(),
                dateTo        : moment().toDate(),
                stockReport   : [],
                connectionError : 0,
            }
        },
        methods: {
           uploadFile(){
                this.csv     = this.$refs.csv.files[0];
                if(this.csv.size > 5*1024*1024){
                    this.loading = 0;
                    this.error   = 1;
                    this.message = 'CSV file size is too big';
                    return;
                }
                this.loading = 1;
                this.success = 0;
                this.error   = 0;
                let formData = new FormData();
                formData.append('csv', this.csv);
                axios.post( Vue.config.publicPath+'/upload',
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
                ).then((response)=>{
                    this.loading = 0;
                    this.success = 1;
                    this.message = response.data.message;
                    this.getStocks();
                    if(response.data.skip_row){
                        this.importError  = response.data.skip_row;
                    }
                })
                .catch((error)=>{
                    this.loading = 0;
                    this.error   = 1;
                    this.message = error.response.statusText;
                });
           },
           getStocks() {
               this.isStockLoading = 1;
               axios.get( Vue.config.publicPath+'/stocks',
                ).then((response)=>{
                    this.stock = response.data;
                    this.isStockLoading = 0;
                })
                .catch((error)=>{
                    this.isStockLoading = 0;
                    this.error   = 1;
                    this.connectionError = 1;
                    this.message = error.response.statusText;
                });
           },
           close(type){
               if(type == 'error'){
                    this.error   = 0;
                }else{
                    this.success = 0;
                }
           },
           report(){
                this.reportLoading = 1;
                this.success = 0;
                this.error   = 0;
                let formData = new FormData();
                formData.append('company', this.company);
                formData.append('dateFrom', moment(this.dateFrom).format('YYYY-MM-DD'));
                formData.append('dateTo', moment(this.dateTo).format('YYYY-MM-DD'));
                axios.post( Vue.config.publicPath+'/stock_report', formData
                ).then((response)=>{
                    this.reportLoading = 0;
                    if(response.data.error){
                        this.error   = 1;
                        this.message = response.data.error;
                        return;
                    }
                    if(response.data['sell_on']){
                      this.stockReport = response.data;
                    }
                })
                .catch((error)=>{
                    this.loading = 0;
                    this.reportLoading = 0;
                    this.error   = 1;
                    this.message = error.response.statusText;
                });
            },  
            customFormatter(date) {
                return moment(date).format('YYYY-MM-DD');
            },
            reset(){
                this.csv           = '';
                this.loading       = 0;
                this.error         = 0;
                this.success       = 0;
                this.message       = '';
                this.company       = '';
                this.reportLoading = 0;
                this.dateFrom      = moment().toDate();
                this.dateTo        = moment().toDate();
                this.stockReport   = [];
            },
            closeImportError(){
                this.importError   = [];
            }
        }
    }
    </script>