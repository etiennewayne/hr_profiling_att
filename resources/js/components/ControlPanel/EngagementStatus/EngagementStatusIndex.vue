<template>
    <div>
        <div class="section">
            <div class="columns is-centered">
                <div class="column is-6-widescreen is-8-desktop is-10-tablet">

                    <div class="box">


                        <div class="is-flex mb-2" style="font-size: 20px; font-weight: bold;">STATUS OF APPOINTMENT</div>
                        <hr>

                        <div class="level">
                            <div class="level-left">
                                <b-field label="Page">
                                    <b-select v-model="perPage" @input="setPerPage">
                                        <option value="10">10 per page</option>
                                        <option value="20">20 per page</option>
                                        <option value="30">30 per page</option>
                                    </b-select>
                                </b-field>
                            </div>

                            <div class="level-right">
                                <div class="level-item">
                                    <b-field label="Search">
                                        <b-input type="text"
                                                    v-model="search.engagement" placeholder="Search Lastname"
                                                    @keyup.native.enter="loadAsyncData"/>
                                        <p class="control">
                                                <b-tooltip label="Search" type="is-success">
                                            <b-button type="is-primary" icon-right="magnify" @click="loadAsyncData"/>
                                                </b-tooltip>
                                        </p>
                                    </b-field>
                                </div>
                            </div>
                        </div>


                        <div class="buttons mt-3">
                            <b-button @click="openModal" 
                                icon-left="account" 
                                class="is-success is-outlined is-small has-text-weight-bold">
                                New SOP
                            </b-button>
                        </div>


                        <b-table
                            :data="data"
                            :loading="loading"
                            paginated
                            backend-pagination
                            :total="total"
                            :pagination-rounded="false"
                            :per-page="perPage"
                            @page-change="onPageChange"
                            aria-next-label="Next page"
                            aria-previous-label="Previous page"
                            aria-page-label="Page"
                            aria-current-label="Current page"
                            backend-sorting
                            :default-sort-direction="defaultSortDirection"
                            @sort="onSort">

                            <b-table-column field="engagement_status_id" label="ID" sortable v-slot="props">
                                {{ props.row.engagement_status_id }}
                            </b-table-column>

                            <b-table-column field="engagement_status" label="Engagement Status" sortable v-slot="props">
                                {{ props.row.engagement_status }}
                            </b-table-column>

                            <b-table-column label="ACTION" v-slot="props">
                                <div class="is-flex">
                                    <b-tooltip label="Edit" type="is-warning">
                                        <b-button class="button is-small mr-1" tag="a" 
                                            icon-right="pencil" 
                                            @click="getData(props.row.engagement_status_id)"></b-button>
                                    </b-tooltip>
                                    <b-tooltip label="Delete" type="is-danger">
                                        <b-button class="button is-small mr-1" icon-right="delete" @click="confirmDelete(props.row.engagement_status_id)"></b-button>
                                    </b-tooltip>
                                    
                                </div>
                            </b-table-column>
                        </b-table>
                        
                    </div>
                </div><!--col -->
            </div><!-- cols -->
        </div><!--section div-->



        <!--modal create-->
        <b-modal v-model="isModalCreate" has-modal-card
            trap-focus
            :width="640"
            aria-role="dialog"
            aria-label="Modal"
            aria-modal>

            <form @submit.prevent="submit">
                <div class="modal-card">
                    <header class="modal-card-head">
                        <p class="modal-card-title">STATUS OF APPOINTMENT</p>
                        <button
                            type="button"
                            class="delete"
                            @click="isModalCreate = false"/>
                    </header>

                    <section class="modal-card-body">
                        <div class="">
                            <div class="columns">
                                <div class="column">
                                    <b-field label="Engagement Status" label-position="on-border"
                                             :type="this.errors.engagement_status ? 'is-danger':''"
                                             :message="this.errors.engagement_status ? this.errors.engagement_status[0] : ''">
                                        <b-input v-model="fields.engagement_status"
                                                 placeholder="Engagement Status" required>
                                        </b-input>
                                    </b-field>
                                </div>
                            </div>
                        </div>
                    </section>

                    <footer class="modal-card-foot">
                        <button
                            :class="btnClass"
                            label="Save"
                            type="is-success">SAVE</button>
                    </footer>

                </div>
            </form><!--close form-->
        </b-modal>
        <!--close modal-->

        


    </div>
</template>

<script>

export default{
    data() {
        return{
            data: [],
            total: 0,
            loading: false,
            sortField: 'engagement_status_id',
            sortOrder: 'desc',
            page: 1,
            perPage: 20,
            defaultSortDirection: 'asc',

            search: {
                engagement: ''
            },

            isModalCreate: false,
            
            fields: {},
            errors: {},

            btnClass: {
                'is-success': true,
                'button': true,
                'is-loading':false,
            },


        }

    },

    methods: {
        /*
        * Load async data
        */
        loadAsyncData() {
            const params = [
                `sort_by=${this.sortField}.${this.sortOrder}`,
                `engagement=${this.search.engagement}`,
                `perpage=${this.perPage}`,
                `page=${this.page}`
            ].join('&')

            this.loading = true
            axios.get(`/get-engagement-status?${params}`)
                .then(({ data }) => {
                    this.data = [];
                    let currentTotal = data.total
                    if (data.total / this.perPage > 1000) {
                        currentTotal = this.perPage * 1000
                    }

                    this.total = currentTotal
                    data.data.forEach((item) => {
                        //item.release_date = item.release_date ? item.release_date.replace(/-/g, '/') : null
                        this.data.push(item)
                    })
                    this.loading = false
                })
                .catch((error) => {
                    this.data = []
                    this.total = 0
                    this.loading = false
                    throw error
                })
        },
        /*
        * Handle page-change event
        */
        onPageChange(page) {
            this.page = page
            this.loadAsyncData()
        },

        onSort(field, order) {
            this.sortField = field
            this.sortOrder = order
            this.loadAsyncData()
        },

        setPerPage(){
            this.loadAsyncData()
        },
        openModal(){
            this.isModalCreate=true;
            this.fields = {};
            this.errors = {};
        },

        submit: function(){
            if(this.fields.role != 'CID'){
                this.fields.cid_sub_role = '';
            }


            if(this.fields.engagement_status_id > 0){
                //update
                axios.put('/engagement-status/'+this.fields.engagement_status_id, this.fields).then(res=>{
                    if(res.data.status === 'updated'){
                        this.$buefy.dialog.alert({
                            title: 'UPDATED!',
                            message: 'Successfully updated.',
                            type: 'is-success',
                            onConfirm: () => {
                                this.loadAsyncData();
                                this.clearFields();
                                this.isModalCreate = false;
                            }
                        })
                    }
                }).catch(err=>{
                    if(err.response.status === 422){
                        this.errors = err.response.data.errors;
                    }
                })
            }else{
                //INSERT HERE
                axios.post('/engagement-status', this.fields).then(res=>{
                    if(res.data.status === 'saved'){
                        this.$buefy.dialog.alert({
                            title: 'SAVED!',
                            message: 'Successfully saved.',
                            type: 'is-success',
                            confirmText: 'OK',
                            onConfirm: () => {
                                this.isModalCreate = false;
                                this.loadAsyncData();
                                this.clearFields();
                            }
                        })
                    }
                }).catch(err=>{
                    if(err.response.status === 422){
                        this.errors = err.response.data.errors;
                    }
                });
            }
        },
        clearFields(){
            this.fields = {};
            this.erros = {}

        },

        getData: async function(data_id){
            this.clearFields();
            this.isModalCreate = true;

            //nested axios for getting the address 1 by 1 or request by request
            await axios.get('/engagement-status/'+data_id).then(res=>{
                this.fields = res.data;
            });

           

        },


        //alert box ask for deletion
        confirmDelete(delete_id) {
            this.$buefy.dialog.confirm({
                title: 'DELETE!',
                type: 'is-danger',
                message: 'Are you sure you want to delete this data?',
                cancelText: 'Cancel',
                confirmText: 'Delete',
                onConfirm: () => this.deleteSubmit(delete_id)
            });
        },
        //execute delete after confirming
        deleteSubmit(delete_id) {
            axios.delete('/engagement-status/' + delete_id).then(res => {
                this.loadAsyncData();
            }).catch(err => {
                if (err.response.status === 422) {
                    this.errors = err.response.data.errors;
                }
            });
        },


    },

    mounted() {
        this.loadAsyncData()
    }

}
</script>


<style scoped>


</style>
