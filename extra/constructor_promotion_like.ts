class Some {
    constructor(private x: number) {

    }

    getX() {
        return this.x
    }
}

console.log((new Some(10)).getX())

