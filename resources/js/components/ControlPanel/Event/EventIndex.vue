<template>
    <div>
        <div class="section">

            <div class="columns is-centered">
                <div class="column is-8-widescreen is-10-tablet">
                    <div class="box">
                        <div class="is-flex is-justify-content-center mb-2" style="font-size: 20px; font-weight: bold;">LIST OF EVENTS</div>

                        <div class="level">
                            <div class="level-left">
                                <b-field label="Page">
                                    <b-select v-model="perPage" @input="setPerPage">
                                        <option value="5">5 per page</option>
                                        <option value="10">10 per page</option>
                                        <option value="15">15 per page</option>
                                        <option value="20">20 per page</option>
                                    </b-select>
                                    <b-select v-model="sortOrder" @input="loadAsyncData">
                                        <option value="asc">ASC</option>
                                        <option value="desc">DESC</option>

                                    </b-select>
                                </b-field>
                            </div>

                            <div class="level-right">
                                <div class="level-item">
                                    <b-field label="Search">
                                        <b-input type="text"
                                                 v-model="search.event" placeholder="Search Event"
                                                 @keyup.native.enter="loadAsyncData"/>
                                        <p class="control">
                                             <b-tooltip label="Search" type="is-success">
                                            <b-button type="is-primary" icon-right="account-filter" @click="loadAsyncData"/>
                                             </b-tooltip>
                                        </p>
                                    </b-field>
                                </div>
                            </div>
                        </div>

                        <div class="buttons mt-3 is-right">
                            <b-button tag="a"
                                href="/events/create"
                                outlined
                                icon-left="plus"
                                class="is-primary is-small">NEW</b-button>
                        </div>

                        <b-table
                            :data="data"
                            :loading="loading"
                            paginated
                            detailed
                            backend-pagination
                            :total="total"
                            :per-page="perPage"
                            @page-change="onPageChange"
                            aria-next-label="Next page"
                            aria-previous-label="Previous page"
                            aria-page-label="Page"
                            aria-current-label="Current page"
                            backend-sorting
                            :default-sort-direction="defaultSortDirection"
                            @sort="onSort">

                            <b-table-column field="event_id" label="ID" v-slot="props">
                                {{ props.row.event_id }}
                            </b-table-column>

                            <b-table-column field="event_datetime" label="Date & Time" v-slot="props">
                                {{ new Date(props.row.event_datetime).toLocaleString() }}
                            </b-table-column>

                            <b-table-column field="event_title" label="Event Title" v-slot="props">
                                {{ props.row.event_title }}
                            </b-table-column>

                            <b-table-column field="content" label="Description" v-slot="props">
                                {{ stripHTML(props.row.event_desc) }}
                            </b-table-column>



                            <b-table-column label="Action" v-slot="props">
                                <div class="is-flex">

                                    <b-tooltip label="View Event" type="is-info">
                                        <b-button class="button is-small mr-1"
                                            icon-right="eye-circle-outline"
                                            tag="a"
                                            :href="`/events-view/${props.row.event_id}`"></b-button>
                                    </b-tooltip>
                                    
                                    <b-tooltip label="Edit" type="is-warning">
                                        <b-button class="button is-small mr-1"
                                            tag="a"
                                            icon-right="pencil" :href="`/events/${props.row.event_id}/edit`" ></b-button>
                                    </b-tooltip>
                                    <b-tooltip label="Delete" type="is-danger">
                                        <b-button class="button is-small mr-1"
                                            icon-right="delete"
                                            @click="confirmDelete(props.row.event_id)"></b-button>
                                    </b-tooltip>

                                    <b-tooltip label="Print Preview" type="is-info">
                                        <b-button class="button is-small mr-1"
                                            icon-right="printer"
                                            @click="printMe(props.row)"></b-button>
                                    </b-tooltip>
                                </div>
                            </b-table-column>

                            <template #detail="props">
                                <img :src="`/storage/events/${props.row.img_path}`" style="height: 200px;" />
                            </template>

                        </b-table>


                    </div>
                </div><!--close column-->
            </div>


        </div><!--section div-->



        <!--modal create-->
        <b-modal v-model="isModalCreate" has-modal-card
                 trap-focus
                 :width="640"
                 aria-role="dialog"
                 aria-label="Modal"
                 aria-modal
                 type = "is-link">

            <form @submit.prevent="submit">
                <div class="modal-card">
                    <header class="modal-card-head">
                        <p class="modal-card-title">Office Information</p>
                        <button
                            type="button"
                            class="delete"
                            @click="isModalCreate = false"/>
                    </header>

                    <section class="modal-card-body">
                        <div class="">
                            <div class="columns">
                                <div class="column">
                                    <b-field label="Office Name"
                                             :type="this.errors.office_name ? 'is-danger':''"
                                             :message="this.errors.office_name ? this.errors.office_name[0] : ''">
                                        <b-input v-model="fields.office_name"
                                                 placeholder="Office Name" required>
                                        </b-input>
                                    </b-field>
                                </div>
                            </div>

                        </div>
                    </section>
                    <footer class="modal-card-foot">
                        <b-button
                            label="Close"
                            @click="isModalCreate=false"/>
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
export default {

    data(){
        return{
            data: [],
            total: 0,
            loading: false,
            sortField: 'event_id',
            sortOrder: 'desc',
            page: 1,
            perPage: 5,
            defaultSortDirection: 'asc',


            global_id : 0,

            search: {
                event: '',
            },

            isModalCreate: false,

            fields: {
                event_title: '',
                event_description: '',

            },
            errors: {},

            btnClass: {
                'is-success': true,
                'button': true,
                'is-loading':false,
            },

        }
    },

    methods: {

     // Method to strip HTML tags
     stripHTML(html) {
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = html;
        return tempDiv.textContent || tempDiv.innerText || "";
    },
        /*
        * Load async data
        */
        loadAsyncData() {
            const params = [
                `sort_by=${this.sortField}.${this.sortOrder}`,
                `event=${this.search.event}`,
                `perpage=${this.perPage}`,
                `page=${this.page}`
            ].join('&')

            this.loading = true
            axios.get(`/get-events?${params}`)
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
            axios.delete('/events/' + delete_id).then(res => {
                this.loadAsyncData();
            }).catch(err => {
                if (err.response.status === 422) {
                    this.errors = err.response.data.errors;
                }
            });
        },

        //update code here
        getData: function(data_id){
            this.clearFields();
            this.global_id = data_id;
            this.isModalCreate = true;


            // //nested axios for getting the address 1 by 1 or request by request
            // axios.get('/offices/'+data_id).then(res=>{
            //     this.fields = res.data;
            // });
        },

        clearFields(){
            this.fields.event_title = ''
            this.fields.event_description = ''
            this.fields.img_path = ''
        },

        printMe(row){
            window.location = 'report-event-attendance/' + row.event_id
        },


        submit: function(){
            if(this.global_id > 0){
                //update
                axios.put('/events/'+this.global_id, this.fields).then(res=>{
                    if(res.data.status === 'updated'){
                        this.$buefy.dialog.alert({
                            title: 'UPDATED!',
                            message: 'Successfully updated.',
                            type: 'is-success',
                            onConfirm: () => {
                                this.loadAsyncData();
                                this.clearFields();
                                this.global_id = 0;
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
                axios.post('/events', this.fields).then(res=>{
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
                                this.global_id = 0;
                            }
                        })
                    }
                }).catch(err=>{
                    if(err.response.status === 422){
                        this.errors = err.response.data.errors;
                    }
                });
            }
        }

    },

    mounted() {

        this.loadAsyncData();
    }

}
</script>

<style scoped>

</style>
