import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';
import axios from 'axios';
import Login from '../Login.vue';

vi.mock('axios');
vi.mock('vue-router', () => ({
    useRouter: () => ({ push: vi.fn() }),
}));

const stubs = {
    AuthLayout: { template: '<div><slot /></div>' },
};

describe('Login.vue', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('renders email and password inputs', () => {
        const wrapper = mount(Login, { global: { stubs } });

        expect(wrapper.find('input[type="email"]').exists()).toBe(true);
        expect(wrapper.find('input[type="password"]').exists()).toBe(true);
        expect(wrapper.find('button[type="submit"]').text()).toBe('Login');
    });

    it('submits credentials via axios.post', async () => {
        axios.get.mockResolvedValue({});
        axios.post.mockResolvedValue({ data: { user: { email: 'a@b.com' } } });

        const wrapper = mount(Login, { global: { stubs } });

        await wrapper.find('input[type="email"]').setValue('a@b.com');
        await wrapper.find('input[type="password"]').setValue('secret');
        await wrapper.find('form').trigger('submit.prevent');
        await flushPromises();

        expect(axios.get).toHaveBeenCalledWith('/sanctum/csrf-cookie');
        expect(axios.post).toHaveBeenCalledWith('/api/login', {
            email: 'a@b.com',
            password: 'secret',
            remember: false,
        });
    });

    it('renders validation errors on 422 response', async () => {
        axios.get.mockResolvedValue({});
        axios.post.mockRejectedValue({
            response: { status: 422, data: { errors: { email: ['Invalid email.'] } } },
        });

        const wrapper = mount(Login, { global: { stubs } });

        await wrapper.find('input[type="email"]').setValue('bad');
        await wrapper.find('input[type="password"]').setValue('x');
        await wrapper.find('form').trigger('submit.prevent');
        await flushPromises();

        expect(wrapper.text()).toContain('Invalid email.');
    });
});
