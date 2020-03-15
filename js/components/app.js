import {router} from "./router.js"

export default ({
    router: router,
    template: `
        <div class="container mx-auto text-center p-4 ">
            <div class="">
                <router-view></router-view>
            </div>
        </div>
    `
});

