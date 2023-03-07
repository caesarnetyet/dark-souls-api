export interface Model {
  id: number
  attributes: {}
  actions: Actions
}

interface Actions {
  edit_url: string
  delete_url: string
}
