import { mount, flushPromises } from '@vue/test-utils'
import { nextTick } from 'vue'
import { describe, it, expect, vi } from 'vitest'
import ModalDialog from '../common/ModalDialog.vue'

describe('ModalDialog.vue', () => {
  it('should call showModal when modelValue changes to true', async () => {
    // -- Mount the component with modelValue initially false
    const wrapper = mount(ModalDialog, {
      props: {
        modelValue: false,
        title: 'Test Modal'
      },
      slots: {
        default: 'Modal Content'
      }
    })

    // -- Get the <dialog> element and stub the showModal method
    const dialog = wrapper.find('dialog').element as HTMLDialogElement
    const showModalSpy = vi.fn()
    dialog.showModal = showModalSpy

    // -- Update modelValue to true
    await wrapper.setProps({ modelValue: true })
    await nextTick()

    expect(showModalSpy).toHaveBeenCalled()
  })

  it('should emit update:modelValue false when clicking the close button', async () => {
    // -- Mount the component with modelValue true (modal open)
    const wrapper = mount(ModalDialog, {
      props: {
        modelValue: true,
        title: 'Test Modal'
      },
      slots: {
        default: 'Modal Content'
      }
    })

    // -- Force the <dialog> element to be open by defining the open property
    const dialog = wrapper.find('dialog').element as HTMLDialogElement
    Object.defineProperty(dialog, 'open', {
      configurable: true,
      value: true
    })
    const closeSpy = vi.fn()
    dialog.close = closeSpy

    // -- Simulate a click on the close button (×)
    const closeButton = wrapper.find('button')
    await closeButton.trigger('click')

    // -- Verify that close() was called and the event was emitted
    expect(closeSpy).toHaveBeenCalled()
    expect(wrapper.emitted('update:modelValue')).toBeTruthy()
    expect(wrapper.emitted('update:modelValue')?.[0]).toEqual([false])
  })

  it('should emit update:modelValue false when the dialog fires the close event', async () => {
    const wrapper = mount(ModalDialog, {
      props: {
        modelValue: true,
        title: 'Test Modal'
      },
      slots: {
        default: 'Modal Content'
      }
    })

    // -- Force the dialog to be open
    const dialog = wrapper.find('dialog').element as HTMLDialogElement
    Object.defineProperty(dialog, 'open', {
      configurable: true,
      value: true
    })

    // -- Dispatch the native close event
    dialog.dispatchEvent(new Event('close'))
    await nextTick()

    // -- Verify that the component emitted the update event with false
    expect(wrapper.emitted('update:modelValue')).toBeTruthy()
    expect(wrapper.emitted('update:modelValue')?.[0]).toEqual([false])
  })
})
